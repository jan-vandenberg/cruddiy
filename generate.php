<pre>
    <?php
      print_r($_POST);
    ?>
</pre>

<?php
include "app/config.php";
include "templates.php";
$tablename = '';
$tabledisplay = '';
$columnname = '' ;
$columndisplay = '';
$columnvisible = '';
$index_table_rows = '';
$index_table_headers = '';

function generate_index($tablename,$tabledisplay,$index_table_headers,$index_table_rows,$column_id) {
  global $indexfile;
  
  $step0 = str_replace("{TABLE_NAME}", $tablename, $indexfile);
  $step1 = str_replace("{TABLE_DISPLAY}", $tabledisplay, $step0);
  $step2 = str_replace("{INDEX_QUERY}", "SELECT * FROM $tablename", $step1 );
  $step3 = str_replace("{INDEX_TABLE_HEADERS}", $index_table_headers, $step2 );
  $step4 = str_replace("{INDEX_TABLE_ROWS}", $index_table_rows, $step3 );
  $step5 = str_replace("{COLUMN_ID}", $column_id, $step4 );
  $step6 = str_replace("{COLUMN_NAME}", $column_id, $step5 );

  $destination_file = fopen("app/".$tablename."-index.php", "w") or die("Unable to open file!");
  fwrite($destination_file, $step6);
  fclose($destination_file);
}

function generate_read($tablename, $column_id, $read_records){
  global $readfile;

  $step0 = str_replace("{TABLE_NAME}", $tablename, $readfile);
  $step1 = str_replace("{TABLE_ID}", $column_id, $step0);
  $step2 = str_replace("{RECORDS_READ_FORM}", $read_records, $step1 );
  
  $destination_file = fopen("app/".$tablename."-read.php", "w") or die("Unable to open file!");
  fwrite($destination_file, $step2);
  fclose($destination_file);
}


function generate_delete($tablename, $column_id){
  global $deletefile;

  $step0 = str_replace("{TABLE_NAME}", $tablename, $deletefile);
  $step1 = str_replace("{TABLE_ID}", $column_id, $step0);
  
  $destination_file = fopen("app/".$tablename."-delete.php", "w") or die("Unable to open file!");
  fwrite($destination_file, $step1);
  fclose($destination_file);
}

function generate_create($tablename,$create_records, $create_err_records, $create_sqlcolumns, $create_numberofparams, $create_sql_params, $create_html, $create_postvars) { 
  global $createfile;
  echo $create_sql_params;

  $step0 = str_replace("{TABLE_NAME}", $tablename, $createfile);
  $step1 = str_replace("{CREATE_RECORDS}", $create_records, $step0);
  $step2 = str_replace("{CREATE_ERR_RECORDS}", $create_err_records, $step1);
  $step3 = str_replace("{CREATE_COLUMN_NAMES}", $create_sqlcolumns, $step2);
  $step4 = str_replace("{CREATE_QUESTIONMARK_PARAMS}", $create_numberofparams, $step3);
  $step5 = str_replace("{CREATE_SQL_PARAMS}", $create_sql_params, $step4 );
  $step6 = str_replace("{CREATE_HTML}", $create_html, $step5);
  $step7 = str_replace("{CREATE_POST_VARIABLES}", $create_postvars, $step6);

  $destination_file = fopen("app/".$tablename."-create.php", "w") or die("Unable to open file!");
  fwrite($destination_file, $step7);
  fclose($destination_file);

}

function generate_update(){
}

function count_index_colums($table) {
    $i = 0; 
    foreach ( $_POST as $key => $value) {
        if ($key == 'singlebutton') {
            //echo "nope";
        }
    else if ($key == $table) {
        foreach ( $_POST[$key] as $columns )
            {
                $column_visible = $columns['columnvisible'];
                //if ($columns['columnvisible'] == 1) {
                if ($column_visible == 1) {
                    $i++;
                }
            }
        }
    }
       return $i;
}
// Go trough the POST array
// Every table is a key
foreach ($_POST as $key => $value) {
    $tablename = '';
    $tabledisplay = '';
    $columnname = '' ;
    $columndisplay = '';
    $columnvisible = '';
    $index_table_rows = '';
    $index_table_headers = '';
    $read_records = '';

    $create_records = '';
    $create_err_records = '';
    $create_sql_columnnames = array();
    $create_numberofparams = '';
    $create_sql_params = array();
    $create_sqlcolumns = array();
    $create_html = array();
    $create_postvars = '';

   if ($key != 'singlebutton') {
        $i = 0;
        $j = 0; 
        $max = count_index_colums($key);
        $total_columns = count($_POST[$key]);
        
    
    //
    //   Generate INDEX pages: one for each selected table
    //
        
    foreach ( $_POST[$key] as $columns ) { 
        if (isset($columns['primary'])){
           $column_id =  $columns['columnname'];
        }
          $column_visible = $columns['columnvisible'];
        //INDEXFILE VARIABLES
        //Get the columns visible in the index file
        //if ($columns['columnvisible'] == 1 && $i < $max) {
        if ($column_visible == 1 && $i < $max) {

            if (isset($columns['columndisplay'])){
                $columndisplay = $columns['columndisplay'];   
            }
            if (empty($columns['columndisplay'])){
                $columndisplay = $columns['columnname'];   
            }
          
            $tablename = $columns['tablename'];
            $tabledisplay = $columns['tabledisplay'];
            $columnname = $columns['columnname'];
           
            $index_table_headers .= 'echo "<th>'.$columndisplay.'</th>";';
            $index_table_rows .= 'echo "<td>" . $row['. "'" . $columnname . "'" . '] . "</td>";';
            $i++;
        }   
              if ($i == $max) {
                generate_index($tablename,$tabledisplay,$index_table_headers,$index_table_rows,$column_id);
        } 
    }
        
    //
    // DETAIL CREATE AND DELETE pages 
    //
    foreach ( $_POST[$key] as $columns ) {
        //print_r($columns);
        if ($j < $total_columns) {

            if (isset($columns['columndisplay'])){
                $columndisplay = $columns['columndisplay'];
                }
            if (empty($columns['columndisplay'])){
                $columndisplay = $columns['columnname'];
                }

        $tablename = $columns['tablename'];
        $tabledisplay = $columns['tabledisplay'];
        $columnname = $columns['columnname'];
        $read_records .= '<div class="form-group">
                        <label>'.$columndisplay.'</label>
                        <p class="form-control-static"><?php echo $row["'.$columnname.'"]; ?></p>
                    </div>';

        $create_records .= "\$$columnname = \"\"; ";
        $create_record = "\$$columnname";
        $create_err_records .= "\$$columnname".'_err'." = \"\"; ";
        $create_err_record = "\$$columnname".'_err';
        $create_sqlcolumns [] = $columnname;   
        $create_sql_params [] = "\$$columnname"; 
        $create_postvars .= "$$columnname = trim(\$_POST[\"$columnname\"]); ";

        $create_html [] = '<div class="form-group">
                            <label>'.$columnname.'</label>
                            <input type="text" name="'. $columnname .'" class="form-control" value="<?php echo '. $create_record. '; ?>">
                            <span class="help-block"><?php echo ' . $create_err_record . ' ;?></span>
                        </div>';
            $j++;    
        }

        if ($j == $total_columns) {
                $create_numberofparams = array_fill(0, $total_columns, '?');
                $create_numberofparams = implode(",", $create_numberofparams);
                $create_sqlcolumns = implode(",", $create_sqlcolumns);
                $create_sql_params = implode(",", $create_sql_params);
                $create_html = implode("\n", $create_html);

                //Generate some stuff
                generate_create($tablename,$create_records, $create_err_records, $create_sqlcolumns, $create_numberofparams, $create_sql_params, $create_html, $create_postvars);
                generate_read($tablename,$column_id,$read_records);
                generate_delete($tablename,$column_id);
            }

   }
 }
}
?>
