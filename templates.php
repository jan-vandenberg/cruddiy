<?php

$indexfile = <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 950px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">{TABLE_DISPLAY} Details</h2>
                        <a href="{TABLE_NAME}-create.php" class="btn btn-success pull-right">Add New Record</a>
                        <a href="index.php" class="btn btn-light pull-right">Back</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";

                    //Pagination
                    if (isset($_GET['pageno'])) {
                        $pageno = $_GET['pageno'];
                    } else {
                        $pageno = 1;
                    }
                    $no_of_records_per_page = 25;
                    $offset = ($pageno-1) * $no_of_records_per_page;

                    $total_pages_sql = "SELECT COUNT(*) FROM {TABLE_NAME}";
                    $result = mysqli_query($link,$total_pages_sql);
                    $total_rows = mysqli_fetch_array($result)[0];
                    $total_pages = ceil($total_rows / $no_of_records_per_page);
                    
                    //Column sorting on column name
                    $orderBy = array('{COLUMNS}'); 
                    $order = '{COLUMN_ID}';
                    if (isset($_GET['order']) && in_array($_GET['order'], $orderBy)) {
                            $order = $_GET['order'];
                        }

                    //Column sort order
                    $sortBy = array('asc', 'desc'); $sort = 'desc';
                    if (isset($_GET['sort']) && in_array($_GET['sort'], $sortBy)) {                                                                    
                          if($_GET['sort']=='asc') {                                                                                                                            
                            $sort='desc';
                            }                                                                                   
                    else {
                        $sort='asc';
                        }                                                                                                                           
                    }
                    // Attempt select query execution
                    $sql = "{INDEX_QUERY} ORDER BY $order $sort LIMIT $offset, $no_of_records_per_page";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        {INDEX_TABLE_HEADERS}
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                    {INDEX_TABLE_ROWS}
                                        echo "<td>";
                                            echo "<a href='{TABLE_NAME}-read.php?{COLUMN_ID}=". $row['{COLUMN_NAME}'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='{TABLE_NAME}-update.php?{COLUMN_ID}=". $row['{COLUMN_NAME}'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='{TABLE_NAME}-delete.php?{COLUMN_ID}=". $row['{COLUMN_NAME}'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
                              ?> <ul class="pagination" align-right>
                                    <li><a href="?pageno=1">First</a></li>
                                    <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                                        <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
                                    </li>
                                    <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                                        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
                                    </li>
                                    <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
                                </ul>
<?php
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
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
    </div>
</body>
</html>
EOT;


$readfile = <<<'EOT'
<?php
// Check existence of id parameter before processing further
if(isset($_GET["{TABLE_ID}"]) && !empty(trim($_GET["{TABLE_ID}"]))){
    // Include config file
    require_once "config.php";

    // Prepare a select statement
    $sql = "SELECT * FROM {TABLE_NAME} WHERE {TABLE_ID} = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
        $param_id = trim($_GET["{TABLE_ID}"]);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                /* Retrieve individual field value
                {INDIVIDUAL_FIELDS}
                $name = $row["name"];
                $address = $row["address"];
                $salary = $row["salary"];
                 */
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }

        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>View Record</h1>
                    </div>
                        
                     {RECORDS_READ_FORM}                    
                    
                    <p><a href="{TABLE_NAME}-index.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
EOT;


$deletefile = <<<'EOT'
<?php
// Process delete operation after confirmation
if(isset($_POST["{TABLE_ID}"]) && !empty($_POST["{TABLE_ID}"])){
    // Include config file
    require_once "config.php";

    // Prepare a delete statement
    $sql = "DELETE FROM {TABLE_NAME} WHERE {TABLE_ID} = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
        $param_id = trim($_POST["{TABLE_ID}"]);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: {TABLE_NAME}-index.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["{TABLE_ID}"]))){
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
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="{TABLE_ID}" value="<?php echo trim($_GET["{TABLE_ID}"]); ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="{TABLE_NAME}-index.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

EOT;

$createfile = <<<'EOT'
<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
{CREATE_RECORDS}
{CREATE_ERR_RECORDS}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
/*    
    // Validate input
    $input_address = trim($_POST["address"]);
    if(empty($input_address)){
        $address_err = "Please enter an address.";
    } else{
        $address = $input_address;
    }

    // Check input errors before inserting in database
    if(empty($name_err) && empty($address_err) && empty($salary_err)){
        // Prepare an insert statement
 */
        {CREATE_POST_VARIABLES}

        $dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
        $options = [
          PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        try {
          $pdo = new PDO($dsn, $db_user, $db_password, $options);
        } catch (Exception $e) {
          error_log($e->getMessage());
          exit('Something weird happened'); //something a user can understand
        }
        $stmt = $pdo->prepare("INSERT INTO {TABLE_NAME} ({CREATE_COLUMN_NAMES}) VALUES ({CREATE_QUESTIONMARK_PARAMS})"); 
        
        if($stmt->execute([ {CREATE_SQL_PARAMS}  ])) {
                $stmt = null;
                header("location: {TABLE_NAME}-index.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add a record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                        {CREATE_HTML}

                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="{TABLE_NAME}-index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
EOT;


$updatefile = <<<'EOT'
<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
{CREATE_RECORDS}
{CREATE_ERR_RECORDS}

// Processing form data when form is submitted
if(isset($_POST["{COLUMN_ID}"]) && !empty($_POST["{COLUMN_ID}"])){
    // Get hidden input value
    ${COLUMN_ID} = $_POST["{COLUMN_ID}"];

    // Field validation

    // Check input errors before inserting in database
//    if(empty($name_err) && empty($address_err) && empty($salary_err)){
        // Prepare an update statement
        
        {CREATE_POST_VARIABLES}

        $dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
        $options = [
          PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        try {
          $pdo = new PDO($dsn, $db_user, $db_password, $options);
        } catch (Exception $e) {
          error_log($e->getMessage());
          exit('Something weird happened'); //something a user can understand
        }
        $stmt = $pdo->prepare("UPDATE {TABLE_NAME} SET {UPDATE_SQL_PARAMS} WHERE {UPDATE_SQL_ID}");


        if(!$stmt->execute([ {UPDATE_SQL_COLUMNS}  ])) {
                echo "Something went wrong. Please try again later.";
                header("location: error.php");
            } else{
                $stmt = null;
                header("location: {TABLE_NAME}-index.php");
            }
} else {
    // Check existence of id parameter before processing further
    if(isset($_GET["{COLUMN_ID}"]) && !empty(trim($_GET["{COLUMN_ID}"]))){
        // Get URL parameter
        ${COLUMN_ID} =  trim($_GET["{COLUMN_ID}"]);

        // Prepare a select statement
        $sql = "SELECT * FROM {TABLE_NAME} WHERE {COLUMN_ID} = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Set parameters
            $param_id = ${COLUMN_ID};

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
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($link);

    }  else{
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
    <title>Update Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">

                        {CREATE_HTML}

                        <input type="hidden" name="{COLUMN_ID}" value="<?php echo ${COLUMN_ID}; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="{TABLE_NAME}-index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

EOT;

$errorfile = <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 750px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Invalid Request</h1>
                    </div>
                    <div class="alert alert-danger fade in">
                        <p>Sorry, you've made an invalid request. Please <a href="index.php" class="alert-link">go back</a> and try again.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
EOT;

$startfile = <<<'EOT'

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select CRUD pages</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
</head>
<fieldset>
<center>
<legend>Available CRUD pages</legend>
<div class="form-group">
    {TABLE_BUTTONS}
</div>
</center>
</fieldset>
EOT;

?>
