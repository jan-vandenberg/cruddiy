<?php
include "app/config.php";
include "helpers.php";

$errors = [];
$schemaFiles = glob('../schema/*.sql');



function extractTableNames($schema) {
    $pattern = '/CREATE TABLE `?(\w+)`?/i';
    preg_match_all($pattern, $schema, $matches);
    return $matches[1] ?? [];
}



if(isset($_POST['submit'])){
    $schemaFile = $_POST['schemaFile'] ?? '';
    $schema = $schemaFile ? file_get_contents($schemaFile) : ($_POST['schema'] ?? '');

    $deleteExisting = isset($_POST['deleteExisting']) ? true : false;

    // Turn off default exception throwing
    mysqli_report(MYSQLI_REPORT_OFF);

    try {
        // Delete existing tables from schema if checkbox is checked
        if ($deleteExisting) {
            $tablesToDelete = extractTableNames($schema);

            mysqli_query($link, "SET FOREIGN_KEY_CHECKS=0;");
            foreach ($tablesToDelete as $tableName) {
                $dropTableSql = "DROP TABLE IF EXISTS `$tableName`;";
                echo $dropTableSql;
                mysqli_query($link, $dropTableSql);
                if (!mysqli_query($link, $dropTableSql)) {
                    $errors['schema'][] = "Something went wrong. Error description: " . mysqli_error($link);
                }
            }
            mysqli_query($link, "SET FOREIGN_KEY_CHECKS=1;");
        }

        // Import new schema
        if (mysqli_multi_query($link, $schema)) {
            do {
                // Store and then free the result set if there is one
                if ($result = mysqli_store_result($link)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_more_results($link) && mysqli_next_result($link));
        }
    } catch (mysqli_sql_exception $e) {
        // Catch and store the error
        $errors['schema'][] = $e->getMessage();
    }

    // Check for any errors that occurred after the last executed query
    if ($error = mysqli_error($link)) {
        $errors['schema'][] = $error;
    }

    mysqli_close($link);

    if (!isset($errors['schema'])) {
        header('location:relations.php');
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD generator</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body class="bg-light">
<section class="pt-5">
    <div class="container bg-white shadow py-5">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="text-center">
                    <h4>Import schema</h4>

                    <?php if (isset($errors['schema'])) : ?>
                        <?php foreach ($errors['schema'] as $error) : ?>
                            <div class="alert alert-danger my-3" role="alert">
                                <?php echo $error ?>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>


                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                        <!-- Dropdown for selecting a schema file -->
                        <div class="form-group my-5">
                            <label for="schemaFile">Select a <code>.sql</code> schema file:</label>
                            <select class="form-control" name="schemaFile" id="schemaFile">
                                <option value=""></option>
                                <?php foreach ($schemaFiles as $file): ?>
                                    <option value="<?php echo htmlspecialchars($file); ?>" <?php echo (isset($_POST['schemaFile']) && $_POST['schemaFile'] == $file) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(basename($file)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small id="schemaFile" class="form-text text-muted">
                                Place your .sql files in the <code>schema/</code> folder in the project root.
                            </small>
                        </div>

                        <!-- Textarea for manual schema copy/paste -->
                        <div class="form-group my-5">
                            <label for="schema">Or copy/paste schema here:</label>
                            <textarea class="form-control" name="schema" id="schema" rows="3"><?php echo isset($_POST['schema']) ? htmlspecialchars($_POST['schema']) : '' ?></textarea>
                        </div>

                        <div class="form-check my-5">
                            <input class="form-check-input" type="checkbox" value="1" id="deleteExisting" name="deleteExisting" <?php echo isset($_POST['deleteExisting']) ? 'checked="checked"' : '' ?>>
                            <label class="form-check-label" for="deleteExisting">
                                Disable foreign key checks and drop existing tables?
                            </label>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary" name="submit">Import schema</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<br>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>
</html>
