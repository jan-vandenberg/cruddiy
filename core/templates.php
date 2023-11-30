<?php

$indexfile = <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{APP_NAME}</title>
    {CSS_REFS}
    <script src="https://kit.fontawesome.com/6b773fe9e4.js" crossorigin="anonymous"></script>
    <style type="text/css">
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 5px;
        }
        body {
            font-size: 14px;
        }
    </style>
</head>
<?php require_once('config.php'); ?>
<?php require_once('helpers.php'); ?>
<?php require_once('navbar.php'); ?>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="float-left"><?php translate('%s Details', true, '{TABLE_DISPLAY}') ?></h2>
                        <a href="{TABLE_NAME}-create.php" class="btn btn-success float-right"><?php translate('Add New Record') ?></a>
                        <a href="{TABLE_NAME}-index.php" class="btn btn-info float-right mr-2"><?php translate('Reset View') ?></a>
                        <a href="javascript:history.back()" class="btn btn-secondary float-right mr-2"><?php translate('Back') ?></a>
                    </div>

                    <div class="form-row">
                        <form action="{TABLE_NAME}-index.php" method="get">
                        <div class="col">
                          <input type="text" class="form-control" placeholder="<?php translate('Search this table') ?>" name="search">
                        </div>
                    </div>
                        </form>
                    <br>

                    <?php
                    //Get current URL and parameters for correct pagination
                    $script   = $_SERVER['SCRIPT_NAME'];
                    $parameters   = $_GET ? $_SERVER['QUERY_STRING'] : "" ;
                    $currenturl = $domain. $script . '?' . $parameters;

                    //Pagination
                    if (isset($_GET['pageno'])) {
                        $pageno = $_GET['pageno'];
                    } else {
                        $pageno = 1;
                    }

                    //$no_of_records_per_page is set on the index page. Default is 10.
                    $offset = ($pageno-1) * $no_of_records_per_page;

                    $total_pages_sql = "SELECT COUNT(*) FROM `{TABLE_NAME}`";
                    $result = mysqli_query($link,$total_pages_sql);
                    $total_rows = mysqli_fetch_array($result)[0];
                    $total_pages = ceil($total_rows / $no_of_records_per_page);

                    //Column sorting on column name
                    $columns = array('{COLUMNS}');
                    // Order by primary key on default
                    $order = '{COLUMN_ID}';
                    if (isset($_GET['order']) && in_array($_GET['order'], $columns)) {
                        $order = $_GET['order'];
                    }

                    //Column sort order
                    $sortBy = array('asc', 'desc'); $sort = 'asc';
                    if (isset($_GET['sort']) && in_array($_GET['sort'], $sortBy)) {
                          if($_GET['sort']=='asc') {
                            $sort='asc';
                            }
                    else {
                        $sort='desc';
                        }
                    }

                    //Generate WHERE statements for param
                    $where_columns = array_intersect_key($_GET, array_flip($columns));
                    $get_param = "";
                    $where_statement = " WHERE 1=1 ";
                    foreach ( $where_columns as $key => $val ) {
                        $where_statement .= " AND `$key` = '" . mysqli_real_escape_string($link, $val) . "' ";
                        $get_param .= "&$key=$val";
                    }

                    if (!empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($link, $_GET['search']);
                        if (strpos('{INDEX_CONCAT_SEARCH_FIELDS}', ',')) {
                            $where_statement .= " AND CONCAT_WS ({INDEX_CONCAT_SEARCH_FIELDS}) LIKE '%$search%'";
                        } else {
                            $where_statement .= " AND {INDEX_CONCAT_SEARCH_FIELDS} LIKE '%$search%'";
                        }

                    } else {
                        $search = "";
                    }

                    $order_clause = !empty($order) ? "ORDER BY `$order` $sort" : '';
                    $group_clause = !empty($order) && $order == '{COLUMN_ID}' ? "GROUP BY `{TABLE_NAME}`.`$order`" : '';

                    // Prepare SQL queries
                    $sql = "SELECT `{TABLE_NAME}`.* {JOIN_COLUMNS}
                            FROM `{TABLE_NAME}` {JOIN_CLAUSES}
                            $where_statement
                            $group_clause
                            $order_clause
                            LIMIT $offset, $no_of_records_per_page;";
                    $count_pages = "SELECT COUNT(*) AS count FROM `{TABLE_NAME}` {JOIN_CLAUSES}
                            $where_statement";

                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            $number_of_results = mysqli_fetch_assoc(mysqli_query($link, $count_pages))['count'];
                            $total_pages = ceil($number_of_results / $no_of_records_per_page);
                            translate('total_results', true, $number_of_results, $pageno, $total_pages);

                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead class='thead-light'>";
                                    echo "<tr>";
                                        {INDEX_TABLE_HEADERS}
                                        echo "<th><?php translate('Actions') ?></th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                    {INDEX_TABLE_ROWS}
                                        echo "<td>";
                                            $column_id = '{COLUMN_ID}';
                                            if (!empty($column_id)) {
                                                echo "<a href='{TABLE_NAME}-read.php?{COLUMN_ID}=". $row['{COLUMN_NAME}'] ."' title='" . addslashes(translate('View Record', false)) ."' data-toggle='tooltip' class='btn btn-sm btn-info'><i class='far fa-eye'></i></a>";
                                                echo "<a href='{TABLE_NAME}-update.php?{COLUMN_ID}=". $row['{COLUMN_NAME}'] ."' title='" . addslashes(translate('Update Record', false)) ."' data-toggle='tooltip' class='btn btn-sm btn-warning'><i class='far fa-edit'></i></a>";
                                                echo "<a href='{TABLE_NAME}-delete.php?{COLUMN_ID}=". $row['{COLUMN_NAME}'] ."' title='" . addslashes(translate('Delete Record', false)) ."' data-toggle='tooltip' class='btn btn-sm btn-danger'><i class='far fa-trash-alt'></i></a>";
                                            } else {
                                                addslashes(translate('unsupported_no_pk'));
                                            }
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
?>
                                <ul class="pagination" align-right>
                                <?php
                                    $new_url = preg_replace('/&?pageno=[^&]*/', '', $currenturl);
                                 ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $new_url .'&pageno=1' ?>"><?php translate('First') ?></a></li>
                                    <li class="page-item <?php if($pageno <= 1){ echo 'disabled'; } ?>">
                                        <a class="page-link" href="<?php if($pageno <= 1){ echo '#'; } else { echo $new_url ."&pageno=".($pageno - 1); } ?>"><?php translate('Prev') ?></a>
                                    </li>
                                    <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                                        <a class="page-link" href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo $new_url . "&pageno=".($pageno + 1); } ?>"><?php translate('Next') ?></a>
                                    </li>
                                    <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                                        <a class="page-item"><a class="page-link" href="<?php echo $new_url .'&pageno=' . $total_pages; ?>"><?php translate('Last') ?></a>
                                    </li>
                                </ul>
<?php
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>" . translate('No records were found.') . "</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }

                    // Close connection
                    mysqli_close($link);
                    ?>
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
EOT;


$readfile = <<<'EOT'
<?php
require_once('config.php');
require_once('helpers.php');

// Check existence of id parameter before processing further
$_GET["{TABLE_ID}"] = trim($_GET["{TABLE_ID}"]);
if(isset($_GET["{TABLE_ID}"]) && !empty($_GET["{TABLE_ID}"])){
    // Prepare a select statement
    $sql = "SELECT `{TABLE_NAME}`.* {JOIN_COLUMNS}
            FROM `{TABLE_NAME}` {JOIN_CLAUSES}
            WHERE `{TABLE_NAME}`.`{TABLE_ID}` = ?
            GROUP BY `{TABLE_NAME}`.`{TABLE_ID}`;";

    if($stmt = mysqli_prepare($link, $sql)){
        // Set parameters
        $param_id = trim($_GET["{TABLE_ID}"]);

        // Bind variables to the prepared statement as parameters
		if (is_int($param_id)) $__vartype = "i";
		elseif (is_string($param_id)) $__vartype = "s";
		elseif (is_numeric($param_id)) $__vartype = "d";
		else $__vartype = "b"; // blob
        mysqli_stmt_bind_param($stmt, $__vartype, $param_id);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }

        } else{
            echo translate('stmt_error') . "<br>".$stmt->error;
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    {CSS_REFS}
</head>
<?php require_once('navbar.php'); ?>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="page-header">
                        <h1>View Record</h1>
                    </div>

                    {RECORDS_READ_FORM}
                    <hr>
                    <p>
                        <a href="{TABLE_NAME}-update.php?{TABLE_ID}=<?php echo $_GET["{TABLE_ID}"];?>" class="btn btn-warning"><?php translate('Update Record') ?></a>
                        <a href="{TABLE_NAME}-delete.php?{TABLE_ID}=<?php echo $_GET["{TABLE_ID}"];?>" class="btn btn-danger"><?php translate('Delete Record') ?></a>
                        <a href="{TABLE_NAME}-index.php" class="btn btn-primary"><?php translate('Back to List') ?></a>
                    </p>
                    <?php
                    {FOREIGN_KEY_REFS}

                    // Close connection
                    mysqli_close($link);
                    ?>
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
EOT;


$deletefile = <<<'EOT'
<?php
require_once('config.php');
require_once('helpers.php');

// Process delete operation after confirmation
if(isset($_POST["{TABLE_ID}"]) && !empty($_POST["{TABLE_ID}"])){

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

EOT;

$createfile = <<<'EOT'
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
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

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
EOT;


$updatefile = <<<'EOT'
<?php
require_once('config.php');
require_once('helpers.php');

// Processing form data when form is submitted
if(isset($_POST["{COLUMN_ID}"]) && !empty($_POST["{COLUMN_ID}"])){
    // Get hidden input value
    ${COLUMN_ID} = $_POST["{COLUMN_ID}"];

    {CREATE_POST_VARIABLES}

    // Prepare an update statement

    $stmt = $link->prepare("UPDATE `{TABLE_NAME}` SET {UPDATE_SQL_PARAMS} WHERE {UPDATE_SQL_ID}");

    try {
        $stmt->execute([ {UPDATE_SQL_COLUMNS}  ]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = $e->getMessage();
    }

    if (!isset($error)){
        header("location: {TABLE_NAME}-read.php?{COLUMN_ID}=${COLUMN_ID}");
    }
}
// Check existence of id parameter before processing further
$_GET["{COLUMN_ID}"] = trim($_GET["{COLUMN_ID}"]);
if(isset($_GET["{COLUMN_ID}"]) && !empty($_GET["{COLUMN_ID}"])){
    // Get URL parameter
    ${COLUMN_ID} =  trim($_GET["{COLUMN_ID}"]);

    // Prepare a select statement
    $sql = "SELECT * FROM `{TABLE_NAME}` WHERE `{COLUMN_ID}` = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        // Set parameters
        $param_id = ${COLUMN_ID};

        // Bind variables to the prepared statement as parameters
        if (is_int($param_id)) $__vartype = "i";
        elseif (is_string($param_id)) $__vartype = "s";
        elseif (is_numeric($param_id)) $__vartype = "d";
        else $__vartype = "b"; // blob
        mysqli_stmt_bind_param($stmt, $__vartype, $param_id);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Retrieve individual field value

                {UPDATE_COLUMN_ROWS}

            } else{
                // URL doesn't contain valid id. Redirect to error page
                header("location: error.php");
                exit();
            }

        } else{
            translate('stmt_error') . "<br>".$stmt->error;
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

}  else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php translate('Update Record') ?></title>
    {CSS_REFS}
</head>
<?php require_once('navbar.php'); ?>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="page-header">
                        <h2><?php translate('Update Record') ?></h2>
                    </div>
                    <?php print_error_if_exists(@$error); ?>
                    <p><?php translate('update_record_instructions') ?></p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">

                        {CREATE_HTML}

                        <input type="hidden" name="{COLUMN_ID}" value="<?php echo ${COLUMN_ID}; ?>"/>
                        <p>
                            <input type="submit" class="btn btn-primary" value="<?php translate('Edit') ?>">
                            <a href="javascript:history.back()" class="btn btn-secondary"><?php translate('Cancel') ?></a>
                        </p>
                        <hr>
                        <p>
                            <a href="{TABLE_NAME}-read.php?{COLUMN_ID}=<?php echo $_GET["{COLUMN_ID}"];?>" class="btn btn-info"><?php translate('View Record') ?></a>
                            <a href="{TABLE_NAME}-delete.php?{COLUMN_ID}=<?php echo $_GET["{COLUMN_ID}"];?>" class="btn btn-danger"><?php translate('Update Record') ?></a>
                            <a href="{TABLE_NAME}-index.php" class="btn btn-primary"><?php translate('Back to List') ?></a>
                        </p>
                        <p><?php translate('required_fiels_instructions') ?></p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
{JS_REFS}
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>

EOT;

$errorfile = <<<'EOT'
<?php
require_once('config.php');
require_once('helpers.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php translate('Error') ?></title>
    {CSS_REFS}
</head>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1><?php translate('Invalid Request') ?></h1>
                    </div>
                    <div class="alert alert-danger fade-in">
                        <p><?php translate('invalid_request_instructions') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {JS_REFS}
</body>
</html>
EOT;

$startfile = <<<'EOT'
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{APP_NAME}</title>
    {CSS_REFS}
    {JS_REFS}

    <style type="text/css">
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 5px;
        }
    </style>
</head>
<?php require_once('config.php'); ?>
<?php require_once('helpers.php'); ?>
<?php require_once('navbar.php'); ?>
</html>
EOT;

$navbarfile = <<<'EOT'
<?php require_once('config-tables-columns.php'); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand nav-link" href="index.php">{APP_NAME}</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php translate('Select Page') ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        {TABLE_BUTTONS}
        <!-- TABLE_BUTTONS -->
        </div>
      </li>
    </ul>
  </div>
</nav>
EOT;


