<?php
if(isset($_POST['server'])) $server=$_POST['server'];
if(isset($_POST['username'])) $username=$_POST['username'];
if(isset($_POST['password'])) $password=$_POST['password'];
if(isset($_POST['database'])) $database=$_POST['database'];
if(isset($_POST['numrecordsperpage'])) $numrecordsperpage=$_POST['numrecordsperpage'];

/* Attempt to connect to MySQL database */
$link = mysqli_connect($server, $username, $password, $database);

// Check connection
 if($link === false){
     die("ERROR: Could not connect. " . mysqli_connect_error());
     }
 else {
     if (!file_exists('app')) {
            mkdir('app', 0777, true);
        }
            $configfile = fopen("app/config.php", "w") or die("Unable to open file!");
            $txt = "<?php \n \$link = mysqli_connect('$server', '$username', '$password', '$database'); \n";
            $txt .= "\$db_server = '$server'; \n";
            $txt .= "\$db_name = '$database'; \n";
            $txt .= "\$db_user = '$username'; \n";
            $txt .= "\$db_password = '$password'; \n";
            $txt .= "\$no_of_records_per_page = $numrecordsperpage; \n?>";
            fwrite($configfile, $txt);
            fclose($configfile);
     }

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD generator</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body>
<section class="pt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <form class="form-horizontal" action="columns.php" method="post">
                    <fieldset>
                        <div class="text-center mb-4">
                            <legend>Available tables</legend>
                        </div>

                        <?php
                        //Get all tables
                        $tablelist = array();
                        $res = mysqli_query($link,"SHOW TABLES");
                        while($cRow = mysqli_fetch_array($res))
                        {
                            $tablelist[] = $cRow[0];
                        }

                        //Loop trough list of tables
                        $i = 0;
                        foreach($tablelist as $table) {

                            echo
                    '<div class="row align-items-center">
                        <div class="col-2 text-right">
                              <label class="control-label" for="table['.$i.'][tablename]">'. $table . ' </label>
                        </div>
                        <div class="col-6">
                                 <input type="hidden" name="table['.$i.'][tablename]" value="'.$table.'"/>
                                 <input id="textinput_'.$table. '" name="table['.$i.'][tabledisplay]" type="text" placeholder="Display table name in frontend" class="form-control">
                        </div>
                        <div class="col-4">
                          <input class="mr-1" type="checkbox"  name="table['.$i.'][tablecheckbox]" id="checkboxes-0" value="1">Generate CRUD
                        </div>
                    </div>
  
  
<br>';

                            $i++;
                        }
                        ?>
                        <div class="row">
                            <div class="col-6 offset-2">
                                <label class="control-label" for="singlebutton"></label>
                                <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Select columns from tables</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
