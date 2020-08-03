<!doctype html>
<html lang="en">
<head>
    <title>Select Columns</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="text-center">
                    <h4 class="mb-0">All available columns</h4>
                </div>
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
                                if (isset($table['tablecheckbox']) && $table['tablecheckbox'] == 1) {
                                    $tablename = $table['tablename'];
                                    $tabledisplay = $table['tabledisplay'];
                                    echo "<div class='text-center my-4'><b>Table: " . $tabledisplay . " (". $tablename .")</b></div>";
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

                                        echo '<div class="row align-items-center mb-2">
                                    <div class="col-2 text-right"
                                        <label class="col-form-label" for="'.$tablename.'">'. $primary . $auto . $column[0] . ' </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][tablename]" value="'.$tablename.'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][tabledisplay]" value="'.$tabledisplay.'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][columnname]" value="'.$column[0].'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][columntype]" value="'.$column_type.'"/>
                                        <input id="textinput_'.$tablename. '"name="'. $tablename. 'columns['.$i.'][columndisplay]" type="text" placeholder="Display table name in frontend" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="checkbox"  name="'.$tablename.'columns['.$i.'][columnvisible]" id="checkboxes-0" value="1">
                                Visible in overview?</div>
                     </div>';
                                        $i++;
                                    }
                                }
                            }
                        }
                        ?>

                        <div class="row">
                            <div class="col-6 offset-2">
                                <label class="col-form-label mt-3" for="singlebutton"></label>
                                <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Generate pages</button>
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
