<?php

require "app/config.php";
require "templates.php";
$tablename = '';
$tabledisplay = '';
$columnname = '' ;
$columndisplay = '';
$columnvisible = '';
$index_table_rows = '';
$index_table_headers = '';
$start_page = '';
$sort = '';
$excluded_keys = array('singlebutton', 'keep_startpage', 'append_links');
$generate_start_checked_links = array();
$startpage_filename = "app/index.php";

function column_type($columnname){
    switch ($columnname) {
        case (preg_match("/text/i", $columnname) ? true : false) :
            return 1;
        break;
        case (preg_match("/enum/i", $columnname) ? true : false) :
            return 2;
        break;
        case (preg_match("/varchar/i", $columnname) ? true : false) :
            return 3;
        break;
            //Tinyint needs work to be selectable from dropdown list
            //So for now a tinyint will be just a input field (in Create)
            //and a prefilled input field (in Update).
        case (preg_match("/tinyint\(1\)/i", $columnname) ? true : false) :
            return 5;
        break;
        case (preg_match("/int/i", $columnname) ? true : false) :
            return 5;
        break;
        default:
            return 0;
        break;
    }
}

function generate_error(){
    global $errorfile;
    $destination_file = fopen("app/error.php", "w") or die("Unable to open file!");
    fwrite($destination_file, $errorfile);
    fclose($destination_file);
    echo "Generating Error file<br><br>";
}

function generate_start($start_page, $keep_startpage, $append_links){
    global $startfile;
    global $generate_start_checked_links;
    global $startpage_filename;

    // make sure that a previous startpage was created before trying to keep it alive
    if (!$keep_startpage || ($keep_startpage && !filesize($startpage_filename))) {
        $step0 = str_replace("{TABLE_BUTTONS}", $start_page, $startfile);
        $destination_file = fopen($startpage_filename, "w") or die("Unable to open fresh startpage file!");
        fwrite($destination_file, $step0);
        fclose($destination_file);
        echo "Generating Startpage file<br>";
    } else {
        if ($append_links) {
            // load existing template
            echo "Retrieving existing Startpage file<br>";
            $handle = fopen($startpage_filename, "r") or die("Unable to open existing startpage file!");;
            $startfile = fread($handle, filesize($startpage_filename));
            fclose($handle);
            
            // extract existing links from app/index.php
            echo "Looking for new links to append to Startpage file<br>";
            $link_matcher_pattern = '/href=["\']?([^"\'>]+)["\']?/im';
            preg_match_all($link_matcher_pattern, $startfile, $startfile_links);
            if (count($startfile_links)) {
                foreach($startfile_links[1] as $startfile_link) {
                    // echo '- Found existing link '.$startfile_link.'<br>';
                }
            }

            // do not append links to app/index.php if they  already 
            preg_match_all($link_matcher_pattern, $start_page, $start_page_links);
            if (count($start_page_links)) {
                foreach($start_page_links[1] as $start_page_link) {
                    if (!in_array($start_page_link, $generate_start_checked_links)) {
                        if (in_array($start_page_link, $startfile_links[1])) {
                            echo '- Not appending '.$start_page_link.' as it already exists<br>';
                        } else {
                            echo '- Appending '.$start_page_link.'<br>';
                            array_push($startfile_links[1], $start_page_link);
                            $linkname = str_replace('-index.php', '', basename($start_page_link));
                            $step0 = preg_replace('/<\/div>.*<\/center>/msx', "\t".'<a href="'.$start_page_link.'" class="btn btn-primary" role="button">'.$linkname.'</a>'."\n</div>\n</center>", $startfile);
                            $destination_file = fopen($startpage_filename, "w") or die("Unable to open file!");
                            fwrite($destination_file, $step0);
                            fclose($destination_file);
                        }
                        array_push($generate_start_checked_links, $start_page_link);
                    }
                }
            }
        }
    }

    
}

function generate_index($tablename,$tabledisplay,$index_table_headers,$index_table_rows,$column_id, $columns_available, $index_sql_search) {
    global $indexfile;
    $columns_available = implode("', '", $columns_available);
    $step0 = str_replace("{TABLE_NAME}", $tablename, $indexfile);
    $step1 = str_replace("{TABLE_DISPLAY}", $tabledisplay, $step0);
    $step2 = str_replace("{INDEX_QUERY}", "SELECT * FROM $tablename", $step1 );
    $step3 = str_replace("{INDEX_TABLE_HEADERS}", $index_table_headers, $step2 );
    $step4 = str_replace("{INDEX_TABLE_ROWS}", $index_table_rows, $step3 );
    $step5 = str_replace("{COLUMN_ID}", $column_id, $step4 );
    $step6 = str_replace("{COLUMN_NAME}", $column_id, $step5 );
    $step7 = str_replace("{COLUMNS}", $columns_available, $step6 );
    $step8 = str_replace("{INDEX_CONCAT_SEARCH_FIELDS}", $index_sql_search, $step7 );
    $destination_file = fopen("app/".$tablename."-index.php", "w") or die("Unable to open file!");
    fwrite($destination_file, $step8);
    fclose($destination_file);
    echo "Generating $tablename Index file(s)<br>";
}

function generate_read($tablename, $column_id, $read_records){
    global $readfile;
    $step0 = str_replace("{TABLE_NAME}", $tablename, $readfile);
    $step1 = str_replace("{TABLE_ID}", $column_id, $step0);
    $step2 = str_replace("{RECORDS_READ_FORM}", $read_records, $step1 );
    $destination_file = fopen("app/".$tablename."-read.php", "w") or die("Unable to open file!");
    fwrite($destination_file, $step2);
    fclose($destination_file);
    echo "Generating $tablename Read file(s)<br>";
}

function generate_delete($tablename, $column_id){
    global $deletefile;
    $step0 = str_replace("{TABLE_NAME}", $tablename, $deletefile);
    $step1 = str_replace("{TABLE_ID}", $column_id, $step0);
    $destination_file = fopen("app/".$tablename."-delete.php", "w") or die("Unable to open file!");
    fwrite($destination_file, $step1);
    fclose($destination_file);
    echo "Generating $tablename Delete file(s)<br><br>";
}

function generate_create($tablename,$create_records, $create_err_records, $create_sqlcolumns, $create_numberofparams, $create_sql_params, $create_html, $create_postvars) {
    global $createfile;
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
    echo "Generating $tablename Create file(s)<br>";
}

function generate_update($tablename, $create_records, $create_err_records, $create_postvars, $column_id, $create_html, $update_sql_params, $update_sql_id, $update_column_rows, $update_sql_columns){
    global $updatefile;
    $step0 = str_replace("{TABLE_NAME}", $tablename, $updatefile);
    $step1 = str_replace("{CREATE_RECORDS}", $create_records, $step0);
    $step2 = str_replace("{CREATE_ERR_RECORDS}", $create_err_records, $step1);
    $step3 = str_replace("{COLUMN_ID}", $column_id, $step2);
    $step4 = str_replace("{UPDATE_SQL_PARAMS}", $update_sql_params, $step3);
    $step5 = str_replace("{UPDATE_SQL_ID}", $update_sql_id, $step4 );
    $step6 = str_replace("{CREATE_HTML}", $create_html, $step5);
    $step7 = str_replace("{CREATE_POST_VARIABLES}", $create_postvars, $step6);
    $step8 = str_replace("{UPDATE_COLUMN_ROWS}", $update_column_rows, $step7);
    $step9 = str_replace("{UPDATE_SQL_COLUMNS}", $update_sql_columns, $step8);
    $destination_file = fopen("app/".$tablename."-update.php", "w") or die("Unable to open file!");
    fwrite($destination_file, $step9);
    fclose($destination_file);
    echo "Generating $tablename Update file(s)<br>";
}

function count_index_colums($table) {
    global $excluded_keys;
    $i = 0;
    foreach ( $_POST as $key => $value) {
        if (in_array($key, $excluded_keys)) {
            //echo "nope";
        }
        else if ($key == $table) {
            foreach ( $_POST[$key] as $columns )
            {
                if (isset($columns['columnvisible'])){
                    $column_visible = $columns['columnvisible'];
                    if ($column_visible == 1) {
                        $i++;
                    }
                }
            }
        }
    }
    return $i;
}

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// Go trough the POST array
// Every table is a key
foreach ($_POST as $key => $value) {
    $tables = array();
    $tablename = '';
    $tabledisplay = '';
    $columnname = '' ;
    $columndisplay = '';
    $columnvisible = '';
    $columns_available = array();
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

    $update_sql_params = array();
    $update_sql_columns = array();
    $update_sql_id = '';
    $update_column_rows = '';

    if (!in_array($key, $excluded_keys)) {
        $i = 0;
        $j = 0;
        $max = count_index_colums($key)+1;
        $total_columns = count($_POST[$key]);
        $total_params = count($_POST[$key]);

        //Specific INDEX page variables
        foreach ( $_POST[$key] as $columns ) {
            if (isset($columns['primary'])){
                $column_id =  $columns['columnname'];
            }

            //INDEXFILE VARIABLES
            //Get the columns visible in the index file
            if (isset($columns['columnvisible'])){
                $column_visible = $columns['columnvisible'];
                if ($columns['columnvisible'] == 1 &&  $i < $max) {

                    $columnname = $columns['columnname'];

                    if (!empty($columns['columndisplay'])){
                        $columndisplay = $columns['columndisplay'];
                    } else {
                        $columndisplay = $columns['columnname'];
                    }

                    $columns_available [] = $columnname;
                    $index_table_headers .= 'echo "<th><a href=?search=$search&sort='.$sort.'&order='.$columnname.'&sort=$sort>'.$columndisplay.'</th>";'."\n\t\t\t\t\t\t\t\t\t\t";
                    $index_table_rows .= 'echo "<td>" . $row['. "'" . $columnname . "'" . '] . "</td>";';
                    $i++;
                }
            }
        }

        //DETAIL CREATE UPDATE AND DELETE pages variables
        foreach ( $_POST[$key] as $columns ) {
            //print_r($columns);
            if ($j < $total_columns) {

                if (isset($columns['columndisplay'])){
                    $columndisplay = $columns['columndisplay'];
                }
                if (empty($columns['columndisplay'])){
                    $columndisplay = $columns['columnname'];
                }

                if (!empty($columns['auto'])){
                    //Dont create html input field for auto-increment columns
                    $j++;
                    $total_params--;
                }

                    //Get all tablenames in an array
                    $tablename = $columns['tablename'];
                    if (!in_array($tablename, $tables))
                    {
                        $tables[$tablename] = $tabledisplay;
                    }

                    $tablename = $columns['tablename'];
                    if (!empty($columns['tabledisplay'])) {
                        $tabledisplay = $columns['tabledisplay'];
                    } else {
                        $tabledisplay = $columns['tablename'];
                    }


                if(empty($columns['auto'])) {

                    $columnname = $columns['columnname'];
                    $read_records .= '<div class="form-group">
                        <label>'.$columndisplay.'</label>
                        <p class="form-control-static"><?php echo $row["'.$columnname.'"]; ?></p>
                    </div>';

                    $create_records .= "\$$columnname = \"\";\n";
                    $create_record = "\$$columnname";
                    $create_err_records .= "\$$columnname".'_err'." = \"\";\n";
                    $create_err_record = "\$$columnname".'_err';
                    $create_sqlcolumns [] = $columnname;
                    $create_sql_params [] = "\$$columnname";
                    $create_postvars .= "$$columnname = trim(\$_POST[\"$columnname\"]);\n\t\t";

                    $update_sql_params [] = "$columnname".'=?';
                    $update_sql_id = "$column_id".'=?';
                    $update_column_rows .= "$$columnname = \$row[\"$columnname\"];\n\t\t\t\t\t";


                    //Foreign Key 
                    //Check if there are foreign keys to take into consideration
                    if(!empty($columns['fk'])){
                        //Get the Foreign Key
                        $sql_getfk = "SELECT i.TABLE_NAME as 'Table', k.COLUMN_NAME as 'Column',
                                k.REFERENCED_TABLE_NAME as 'FK Table', k.REFERENCED_COLUMN_NAME as 'FK Column',
                                i.CONSTRAINT_NAME as 'Constraint Name'
                                FROM information_schema.TABLE_CONSTRAINTS i
                                LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                                WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND k.TABLE_NAME = '$tablename' AND k.COLUMN_NAME = '$columnname'";
                        $result = mysqli_query($link, $sql_getfk);
                        if (mysqli_num_rows($result) > 0) {
                          while($row = mysqli_fetch_assoc($result)) {
                            $fk_table = $row["FK Table"];
                            $fk_column = $row["FK Column"];
                          }

                        
                        //Be careful code below is particular regarding single and double quotes.

                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                                <select class="form-control" id="'. $columnname .'" name="'. $columnname .'">
                                <?php
                                      $sql = "SELECT *,'. $fk_column .' FROM '. $fk_table . '";
                                      $result = mysqli_query($link, $sql);
                                      while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                        array_pop($row);
                                        $value = implode(" | ", $row);
                                        if ($row["' . $fk_column . '"] == $' . $columnname . '){
                                           echo \'<option value="\' . "$row['. $fk_column. ']" . \'"selected="selected">\' . "$value" . \'</option>\';
                                        } else {
                                            echo \'<option value="\' . "$row['. $fk_column. ']" . \'">\' . "$value" . \'</option>\';
                                      }
                                    }
                                ?>
                                </select>
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                        </div>';
                    }

            // No Foreign Keys, just regular columns from here on
            } else {

                    $type = column_type($columns['columntype']);

                    switch($type) {
                    //TEXT
                    case 0:
                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                            <input type="text" name="'. $columnname .'" class="form-control" value="<?php echo '. $create_record. '; ?>">
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                        </div>';
                    break;

                    //ENUM types
                    case 2:
                    //Make sure on the update form that the previously selected type is also selected from the list    
                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                            <select name="'.$columnname.'" class="form-control" id="'.$columnname .'">';
                        $create_html [] .=  '<?php 
                                        $sql_enum = "SELECT COLUMN_TYPE as AllPossibleEnumValues 
                                        FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '. "'$tablename'" .'  AND COLUMN_NAME = '."'$columnname'" .'";
                                        $result = mysqli_query($link, $sql_enum);
                                        while($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                                        preg_match(\'/enum\((.*)\)$/\', $row[0], $matches);
                                        $vals = explode("," , $matches[1]);
                                        foreach ($vals as $val){
                                            $val = substr($val, 1);
                                            $val = rtrim($val, "\'");
                                            if ($val == $'.$columnname.'){
                                               echo \'<option value="\' . $val . \'" selected="selected">\' . $val . \'</option>\';
                                            } else 
                                               echo \'<option value="\' . $val . \'">\' . $val . \'</option>\';
                                                    }
                                           }?>';

                        $create_html [] .= '</select>
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                            </div>';
                    break;
                    //VARCHAR
                    case 3:
                        preg_match('#\((.*?)\)#', $columns['columntype'], $match);
                        $maxlength = $match[1];
                        
                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                            <input type="text" name="'. $columnname .'" maxlength="'.$maxlength.'"class="form-control" value="<?php echo '. $create_record. '; ?>">
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                        </div>';
                    break;
                    //TINYINT - Will never hit. Needs work.
                    case 4:
                        $regex = "/'(.*?)'/";
                        preg_match_all( $regex , $columns['columntype'] , $enum_array );

                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                            <select name="'.$columnname.'" class="form-control" id="'.$columnname .'">';
                                    $create_html [] .= '    <option value="0">0</option>';
                                    $create_html [] .= '    <option value="1">1</option>';
                        $create_html [] .= '</select>
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                            </div>';
                    break;
                    //INT
                    case 5:
                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                            <input type="number" name="'. $columnname .'" class="form-control" value="<?php echo '. $create_record. '; ?>">
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                        </div>';
                    break;

                    default:
                        $create_html [] = '<div class="form-group">
                            <label>'.$columndisplay.'</label>
                            <textarea name="'. $columnname .'" class="form-control"><?php echo '. $create_record. ' ; ?></textarea>
                            <span class="form-text"><?php echo ' . $create_err_record . '; ?></span>
                        </div>';
                    break;
                    }
                }
                    $j++;
                }
            }
            if ($j == $total_columns) {

                $update_sql_columns = $create_sql_params;
                $update_sql_columns [] = "\$$column_id";
                $update_sql_columns = implode(",", $update_sql_columns);

                $index_sql_search = implode(",", $columns_available);
                $create_numberofparams = array_fill(0, $total_params, '?');
                $create_numberofparams = implode(",", $create_numberofparams);
                $create_sqlcolumns = implode(",", $create_sqlcolumns);
                $create_sql_params = implode(",", $create_sql_params);
                $create_html = implode("\n\t\t\t\t\t\t", $create_html);

                $update_sql_params = implode(",", $update_sql_params);

                //Generate everything
                $start_page .= "";

                foreach($tables as $key => $value) {
                    //echo "$key is at $value";
                    $start_page .= '<a href="'. $key . '-index.php" class="btn btn-primary" role="button">'. $value. '</a> ';
                    $start_page .= "\n\t";
                }

                generate_start($start_page, isset($_POST['keep_startpage']) && $_POST['keep_startpage'] == 'true' ? true : false, isset($_POST['append_links']) && $_POST['append_links'] == 'true' ? true : false);
                generate_error();
                generate_index($tablename,$tabledisplay,$index_table_headers,$index_table_rows,$column_id, $columns_available,$index_sql_search);
                generate_create($tablename,$create_records, $create_err_records, $create_sqlcolumns, $create_numberofparams, $create_sql_params, $create_html, $create_postvars);
                generate_read($tablename,$column_id,$read_records);
                generate_update($tablename, $create_records, $create_err_records, $create_postvars, $column_id, $create_html, $update_sql_params, $update_sql_id, $update_column_rows, $update_sql_columns);
                generate_delete($tablename,$column_id);
            }
        }

    }

}
?>
<br>Your app has been created! It is completely self contained in the /app folder. You can move this folder anywhere on your server.<br><br>
<a href="app/index.php" target="_blank" rel="noopener noreferrer">Go to your app</a> (this will open your app in a new tab).<br><br>
You can close this tab or leave it open and use the back button to make changes and regenerate the app. Every run will overwrite the previous app.<br>
If you need further instructions please visit <a href="http://cruddiy.com">cruddiy.com</a>
