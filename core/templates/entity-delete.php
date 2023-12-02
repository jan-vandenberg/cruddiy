<?php
require_once('config.php');
require_once('helpers.php');
require_once('config-tables-columns.php');

// Process delete operation after confirmation
if(isset($_POST["{TABLE_ID}"]) && !empty($_POST["{TABLE_ID}"])){


    // Find uploaded files references for deletion
    $fileColumns = [];
    if (isset($tables_and_columns_names['{TABLE_NAME}']['columns'])) {
        foreach ($tables_and_columns_names['{TABLE_NAME}']['columns'] as $columnName => $columnDetails) {
            if (isset($columnDetails['is_file']) && $columnDetails['is_file'] == 1) {
                $fileColumns[] = $columnName;
            }
        }
    }

    if (!empty($fileColumns)) {
        foreach ($fileColumns as $columnName) {

            $sql = "SELECT `" . $columnName . "`
                    FROM `{TABLE_NAME}`
                    WHERE `{TABLE_ID}` = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Set parameters
                $param_id = trim($_POST["{TABLE_ID}"]);

                // Bind variables
                if (is_int($param_id)) $__vartype = "i";
                elseif (is_string($param_id)) $__vartype = "s";
                elseif (is_numeric($param_id)) $__vartype = "d";
                else $__vartype = "b"; // blob
                mysqli_stmt_bind_param($stmt, $__vartype, $param_id);

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) == 1) {
                        /* Fetch result row as an associative array. Since the result set
                        contains only one row, we don't need to use while loop */
                        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                        $fileToDelete = "$upload_target_dir" . $row[$columnName];
                        if (file_exists($fileToDelete)) {
                            // echo "Delete $columnName: " . $row[$columnName];
                            unlink($fileToDelete);
                        }
                    }
                    else {
                        // URL doesn't contain valid id parameter. Redirect to error page
                        header("location: error.php");
                        exit();
                    }

                }
                else {
                    echo translate('stmt_error') . "<br>" . $stmt->error;
                }
            }
        }
    }



    // Prepare a delete statement
    $sql = "DELETE FROM `{TABLE_NAME}` WHERE `{TABLE_ID}` = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Set parameters
        $param_id = trim($_POST["{TABLE_ID}"]);

        // Bind variables to the prepared statement as parameters
		if (is_int($param_id)) $__vartype = "i";
		elseif (is_string($param_id)) $__vartype = "s";
		elseif (is_numeric($param_id)) $__vartype = "d";
		else $__vartype = "b"; // blob
        mysqli_stmt_bind_param($stmt, $__vartype, $param_id);

        try {
            mysqli_stmt_execute($stmt);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $error = $e->getMessage();
        }

        if (!isset($error)){
            // Records deleted successfully. Redirect to landing page
            header("location: {TABLE_NAME}-index.php");
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter
	$_GET["{TABLE_ID}"] = trim($_GET["{TABLE_ID}"]);
    if(empty($_GET["{TABLE_ID}"])){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php translate ('Delete Record') ?></title>
    {CSS_REFS}
</head>
<?php require_once('navbar.php'); ?>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="page-header">
                        <h1><?php translate ('Delete Record') ?></h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?{TABLE_ID}=" . $_GET["{TABLE_ID}"]; ?>" method="post">
                    <?php print_error_if_exists(@$error); ?>
                        <div class="alert alert-danger fade-in">
                            <input type="hidden" name="{TABLE_ID}" value="<?php echo trim($_GET["{TABLE_ID}"]); ?>"/>
                            <p><?php translate('delete_record_confirm') ?></p><br>
                            <p>
                                <input type="submit" value="<?php translate('Yes') ?>" class="btn btn-danger">
                                <a href="javascript:history.back()" class="btn btn-secondary"><?php translate('No') ?></a>
                            </p>
                        </div>
                    </form>
                    <hr>
                    <p>
                        <a href="{TABLE_NAME}-read.php?{TABLE_ID}=<?php echo $_GET["{TABLE_ID}"];?>" class="btn btn-info"><?php translate('View Record') ?></a>
                        <a href="{TABLE_NAME}-update.php?{TABLE_ID}=<?php echo $_GET["{TABLE_ID}"];?>" class="btn btn-warning"><?php translate('Update Record') ?></a>
                        <a href="javascript:history.back()" class="btn btn-primary"><?php translate('Back') ?></a>
                    </p>
                </div>
            </div>
        </div>
    </section>
{JS_REFS}
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>