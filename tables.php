<?php
if(isset($_POST['server'])) $server=$_POST['server'];
if(isset($_POST['username'])) $username=$_POST['username'];
if(isset($_POST['password'])) $password=$_POST['password'];
if(isset($_POST['database'])) $database=$_POST['database'];

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
            $txt .= "\$db_password = '$password'; \n?>";
            fwrite($configfile, $txt);
            fclose($configfile);
     }

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD generator</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
</head>
<br>
<form class="form-horizontal" action="columns.php" method="post">
<fieldset>
<center>
<legend>Availabe tables</legend>
</center>

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
            
  '<div class="form-group">
    <label class="col-md-4 control-label" for="table['.$i.'][tablename]">'. $table . ' </label>
    <div class="col-md-4">
        <input type="hidden" name="table['.$i.'][tablename]" value="'.$table.'"/>
        <input id="textinput_'.$table. '" name="table['.$i.'][tabledisplay]" type="text" placeholder="Display table name in frontend" class="form-control input-md">
    </div>
        <input type="checkbox"  name="table['.$i.'][tablecheckbox]" id="checkboxes-0" value="1">
                Generate CRUD
  </div>
<br>'; 

    $i++;
    }
?>
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Select columns from tables</button>
  </div>
</div>
</fieldset>
</form>
