<html>
<head>
    <title>Select Columns</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
</head>
<body>
<center>
<legend>All availabe columns</legend>
</center>
<form class="form-horizontal" action="generate.php" method="post">
<fieldset>
<?php

include "app/config.php";

function get_primary_keys($table){
    global $link;
    $sql = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result))
    {
        $primary_keys[] = $row['Column_name'];
    }
    return $primary_keys; 
}

function get_autoincrement_cols($table){
    global $link;
    $sql = "DESCRIBE $table";
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result))
    {
        if ($row['Extra'] == 'auto_increment') {
            $auto_keys[] = $row['Field'];
        }
    }
    return $auto_keys;
}

function get_col_types($table,$column){
    global $link;
    $sql = "SHOW FIELDS FROM $table where FIELD ="."'".$column."'";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    return $row['Type'] ;
    mysqli_free_result($result);
}

if ( isset( $_POST['table'] ) )
{
    foreach ( $_POST['table'] as $table )
    {
        $i=0;
        if ($table['tablecheckbox'] == 1) {
            $tablename = $table['tablename'];
            $tabledisplay = $table['tabledisplay'];
                echo "<center><b>Table: " . $tabledisplay . " (". $tablename .")</b></center>";
            $sql = "SHOW columns FROM $tablename"; 
            $primary_keys = get_primary_keys($tablename);
            $auto_keys = get_autoincrement_cols($tablename);

            //print_r($primary_keys);

        $result = mysqli_query($link,$sql);
        while ($column = mysqli_fetch_array($result)) {

        $column_type = get_col_types($tablename,$column[0]);
//        echo $column_type;

        if (in_array ("$column[0]", $primary_keys)) {
               $primary = "🔑";
               echo '<input type="hidden" name="'.$tablename.'columns['.$i.'][primary]" value="'.$primary.'"/>';
            }
        else {
            $primary = "";
        }

        if (in_array ("$column[0]", $auto_keys)) {
               $auto = "🔒";
               echo '<input type="hidden" name="'.$tablename.'columns['.$i.'][auto]" value="'.$auto.'"/>';
            }
          else {
            $auto = "";
        }

        echo '<div class="form-group">
                    <label class="col-md-4 control-label" for="'.$tablename.'">'. $primary . $auto . $column[0] . ' </label>
                        <div class="col-md-4">
                            <input type="hidden" name="'.$tablename.'columns['.$i.'][tablename]" value="'.$tablename.'"/>
                            <input type="hidden" name="'.$tablename.'columns['.$i.'][tabledisplay]" value="'.$tabledisplay.'"/>
                            <input type="hidden" name="'.$tablename.'columns['.$i.'][columnname]" value="'.$column[0].'"/>
                            <input type="hidden" name="'.$tablename.'columns['.$i.'][columntype]" value="'.$column_type.'"/>
                            <input id="textinput_'.$tablename. '"name="'. $tablename. 'columns['.$i.'][columndisplay]" type="text" placeholder="Display table name in frontend" class="form-control input-md">
                        </div>
                        <input type="checkbox"  name="'.$tablename.'columns['.$i.'][columnvisible]" id="checkboxes-0" value="1">
                        Visible in overview?
             </div>';
        $i++;
            }
        }
    }
}
?>

<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Generate pages</button>
  </div>
</div>
</fieldset>
</form> 

