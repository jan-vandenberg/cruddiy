<?php
include "app/config.php";
$errors = [];

if(isset($_POST['submit'])){
    $schema = $_POST['schema'];

    // Turn off default exception throwing
    mysqli_report(MYSQLI_REPORT_OFF);

    try {
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
                <h4 class="mb-0">Import schema</h4>

                <?php if (isset($errors['schema'])) : ?>
                    <?php foreach ($errors['schema'] as $error) : ?>
                        <div class="alert alert-danger my-3" role="alert">
                            <?php echo $error ?>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>

                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <label for="schema">If you have an existing schema, copy/paste it below:</label>
                        <textarea class="form-control" name="schema" id="schema" rows="3"></textarea>
                    </div>
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
