<?php
require_once('config.php');
require_once('helpers.php');

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    {CREATE_POST_VARIABLES}

    $stmt = $link->prepare("INSERT INTO `{TABLE_NAME}` ({CREATE_COLUMN_NAMES}) VALUES ({CREATE_QUESTIONMARK_PARAMS})");

    try {
        $stmt->execute([ {CREATE_SQL_PARAMS} ]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = $e->getMessage();
    }

    if (!isset($error)){
        $new_id = mysqli_insert_id($link);
        header("location: {TABLE_NAME}-read.php?{COLUMN_ID}=$new_id");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php translate('Add New Record') ?></title>
    {CSS_REFS}
</head>
<?php require_once('navbar.php'); ?>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="page-header">
                        <h2><?php translate('Add New Record') ?></h2>
                    </div>
                    <?php print_error_if_exists(@$error); ?>
                    <p><?php translate('add_new_record_instructions') ?></p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

                        {CREATE_HTML}

                        <input type="submit" class="btn btn-primary" value="<?php translate('Create') ?>">
                        <a href="{TABLE_NAME}-index.php" class="btn btn-secondary"><?php translate('Cancel') ?></a>
                    </form>
                    <p><small><?php translate('required_fiels_instructions') ?></small></p>
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