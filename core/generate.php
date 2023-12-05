<?php
// Debug POST data
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
// exit();

$total_postvars = count($_POST, COUNT_RECURSIVE);
$max_postvars = ini_get("max_input_vars");
if ($total_postvars >= $max_postvars) {
    echo "Uh oh, it looks like you're trying to use more variables than your PHP settings (<a href='https://www.php.net/manual/en/info.configuration.php#ini.max-input-vars'>max_input_variables</a>) allow! <br>";
    echo "Go back and choose less tables and/or columns or change your php.ini setting. <br>";
    echo "Read <a href='https://betterstudio.com/blog/increase-max-input-vars-limit/'>here</a> how you can increase this limit.<br>";
    echo "Cruddiy will now exit because only part of what you wanted would otherwise be generated. 🙇";
    exit();
}

require "app/config.php";
require "templates.php";
require "helpers.php";
$tablename = '';
$tabledisplay = '';
$columnname = '' ;
$columndisplay = '';
$columnvisible = '';
$index_table_rows = '';
$index_table_headers = '';
$sort = '';
$excluded_keys = array('singlebutton', 'keep_startpage', 'append_links');
$generate_start_checked_links = array();
$startpage_filename = "app/navbar.php";
$forced_deletion = false;
$buttons_delimiter = '<!-- TABLE_BUTTONS -->';

$CSS_REFS = '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">';
// $CSS_REFS = '<link rel="stylesheet" href="css/style.css" type="text/css"/>
// <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>';

$JS_REFS = '<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>';
// $JS_REFS = '<script src="js/jquery-3.5.1.min.js"></script>
// <script src="js/popper.min.js"></script>
// <script src="js/bootstrap.min.js"></script>';


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
        case (preg_match("/char/i", $columnname) ? true : false) :
            return 9;
        break;
        case (preg_match("/tinyint\(1\)/i", $columnname) ? true : false) :
            return 4;
        break;
        case (preg_match("/int/i", $columnname) ? true : false) :
            return 5;
        break;
        case (preg_match("/decimal/i", $columnname) ? true : false) :
            return 6;
        break;
        case (preg_match("/datetime/i", $columnname) ? true : false) :
            return 8;
        break;
        case (preg_match("/date/i", $columnname) ? true : false) :
            return 7;
        break;
        default:
            return 0;
        break;
    }
}

function is_primary_key($t, $c){
    $cols = $_POST[$t . 'columns'];
    foreach($cols as $col) {
        if (isset($col['primary']) && $col['columnname'] == $c){
            return 1;
        }
    }
    return 0;
}

function get_sql_concat_select($copy_columns, $table, $name){
    $array = $copy_columns;
    if (is_array($copy_columns)) {
        foreach ($array as $key => $c) {
            $array[$key] = '`' . $table . '`.`' . $array[$key] . '`';
        }
        return "\n\t\t\t, CONCAT_WS(' | '," . implode(', ', $array) . ') AS `' . $name . '`';
    } else {
        return '';
    }
}

function get_sql_select($copy_columns){
    $array = $copy_columns;
    foreach($array as $key => $c)
    {
        $array[$key] = '`'.$array[$key].'`';
    }
    return implode(', ', $array);
}

function generate_error(){
    global $errorfile;
    global $CSS_REFS;
    global $JS_REFS;

    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $errorfile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);

    if (!file_put_contents("app/error.php", $prestep2, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating Error file<br>";
}

function generate_startpage(){
    global $appname;
    global $startfile;
    global $CSS_REFS;
    global $JS_REFS;

    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $startfile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);
    $prestep3 = str_replace("{APP_NAME}", $appname, $prestep2);

    if (!file_put_contents("app/index.php", $prestep3, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating main index file.<br>";

}

function generate_navbar($tablename, $start_page, $keep_startpage, $append_links, $tabledisplay, $key){
    global $navbarfile;
    global $generate_start_checked_links;
    global $startpage_filename;

    echo "<h3>Table: $tablename</h3>";

    // make sure that a previous startpage was created before trying to keep it alive
    if (!$keep_startpage || ($keep_startpage && !file_exists($startpage_filename))) {
        if (!file_exists($startpage_filename)) {
            // called on the first run of the POST loop
            echo "Generating fresh Startpage file<br>";
            $step0 = str_replace("{TABLE_BUTTONS}", $start_page, $navbarfile);
            if (!file_put_contents($startpage_filename, $step0, LOCK_EX)) {
                die("Unable to open fresh startpage file!");
            }
        } else {
            // called on subsequent runs of the POST loop
            echo "Populating Startpage file<br>";
            $navbarfile = file_get_contents($startpage_filename);
            if (!$navbarfile) {
                die("Unable to open existing startpage file!");
            }
            append_links_to_navbar($navbarfile, $start_page, $startpage_filename, $generate_start_checked_links, $key);
        }
    } else {
        if ($append_links) {
            // load existing template
            echo "Retrieving existing Startpage file<br>";
            $navbarfile = file_get_contents($startpage_filename);
            if (!$navbarfile) {
                die("Unable to open existing startpage file!");
            }
            append_links_to_navbar($navbarfile, $start_page, $startpage_filename, $generate_start_checked_links, $key);
        }
    }
}

function append_links_to_navbar($navbarfile, $start_page, $startpage_filename, $generate_start_checked_links, $key) {

    global $buttons_delimiter;
    global $appname;

    // extract existing links from app/index.php
    echo "Looking for new link to append to Startpage file<br>";
    $navbarfile_appended = $navbarfile;
    $link_matcher_pattern = '/href=["\']?([^"\'>]+)["\']?/im';
    preg_match_all($link_matcher_pattern, $navbarfile, $navbarfile_links);
    if (count($navbarfile_links)) {
        foreach($navbarfile_links[1] as $navbarfile_link) {
            // echo '- Found existing link '.$navbarfile_link.'<br>';
        }
    }

    // do not append links to app/index.php if they already exist
    preg_match_all($link_matcher_pattern, $start_page, $start_page_links);
    if (count($start_page_links)) {
        foreach($start_page_links[1] as $start_page_link) {
            if (!in_array($start_page_link, $generate_start_checked_links)) {
                if (in_array($start_page_link, $navbarfile_links[1])) {
                    echo '- Not appending '.$start_page_link.' as it already exists<br>';
                } else {
                    echo '- Appending '.$start_page_link.'<br>';
                    array_push($navbarfile_links[1], $start_page_link);
                    $button_string = "\t".'<a class="dropdown-item" href="'.$start_page_link.'"><?php echo $tables_and_columns_names["'.$key.'"]["name"] ?></a>'."\n\t".$buttons_delimiter;
                    $button_string = "\t".'<a class="dropdown-item" href="'.$start_page_link.'"><?php echo (!empty($tables_and_columns_names["'.$key.'"]["name"])) ? $tables_and_columns_names["'.$key.'"]["name"] : "'.$key.'" ?></a>'."\n\t".$buttons_delimiter;
                    $step0 = str_replace($buttons_delimiter, $button_string, $navbarfile);
                    $step1 = str_replace("{APP_NAME}", $appname, $step0 );
                    if (!file_put_contents($startpage_filename, $step1, LOCK_EX)) {
                        die("Unable to open file!");
                    }
                }
                array_push($generate_start_checked_links, $start_page_link);
            }
        }
    }
}


function generate_index($tablename,$tabledisplay,$index_table_headers,$index_table_rows,$column_id, $columns_available, $index_sql_search, $join_columns, $join_clauses) {

    // Load template
    $template = "templates/entity-index.php";
    if (file_exists($template)) {
        // Read the file's content into a variable
        $indexfile = file_get_contents($template);
    } else {
        exit("File $template does not exist.");
    }

    global $appname;
    global $CSS_REFS;
    global $JS_REFS;

    // echo '<pre>';
    // print_r($columns_available);
    // echo '</pre>';

    /*
    echo "<br><strong>tablename</strong>:<pre>";
    print_r(htmlspecialchars($tablename));
    echo "</pre><strong>tabledisplay</strong>:<pre>";
    print_r(htmlspecialchars($tabledisplay));
    echo "</pre><strong>index_table_headers</strong>:<pre>";
    print_r(htmlspecialchars($index_table_headers));
    echo "</pre><strong>index_table_rows</strong>:<pre>";
    print_r(htmlspecialchars($index_table_rows));
    echo "</pre><strong>column_id</strong>:<pre>";
    print_r(htmlspecialchars($column_id));
    echo "</pre><strong>columns_available</strong>:<pre>";
    print_r(htmlspecialchars(print_r($columns_available, true)));
    echo "</pre><strong>index_sql_search</strong>:<pre>";
    print_r(htmlspecialchars($index_sql_search));
    echo "</pre><strong>join_columns</strong>:<pre>";
    print_r(htmlspecialchars($join_columns));
    echo "</pre><strong>join_clauses</strong>:<pre>";
    print_r(htmlspecialchars($join_clauses));
    echo "</pre>";
    */


    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $indexfile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);

    $columns_available = implode("', '", $columns_available);
    $step1 = str_replace("{TABLE_NAME}", $tablename, $prestep2);
    $step2 = str_replace("{TABLE_DISPLAY}", $tabledisplay, $step1);
    $step3 = str_replace("{INDEX_TABLE_HEADERS}", $index_table_headers, $step2 );
    $step4 = str_replace("{INDEX_TABLE_ROWS}", $index_table_rows, $step3 );
    $step5 = str_replace("{COLUMN_ID}", $column_id, $step4 );
    $step6 = str_replace("{COLUMN_NAME}", $column_id, $step5 );
    $step7 = str_replace("{COLUMNS}", $columns_available, $step6 );
    $step8 = str_replace("{INDEX_CONCAT_SEARCH_FIELDS}", $index_sql_search, $step7 );
    $step9 = str_replace("{APP_NAME}", $appname, $step8 );
    $step10 = str_replace("{JOIN_COLUMNS}", $join_columns, $step9 );
    $step11 = str_replace("{JOIN_CLAUSES}", $join_clauses, $step10 );
    if (!file_put_contents("app/".$tablename."-index.php", $step11, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating $tablename Index file<br>";
}

function generate_read($tablename, $column_id, $read_records, $foreign_key_references, $join_columns, $join_clauses){

    // Load template
    $template = "templates/entity-read.php";
    if (file_exists($template)) {
        // Read the file's content into a variable
        $readfile = file_get_contents($template);
    } else {
        exit("File $template does not exist.");
    }

    global $CSS_REFS;
    global $JS_REFS;

    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $readfile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);

    $step0 = str_replace("{TABLE_NAME}", $tablename, $prestep2);
    $step1 = str_replace("{TABLE_ID}", $column_id, $step0);
    $step2 = str_replace("{RECORDS_READ_FORM}", $read_records, $step1 );
    $step3 = str_replace("{FOREIGN_KEY_REFS}", $foreign_key_references, $step2 );
    $step4 = str_replace("{JOIN_COLUMNS}", $join_columns, $step3 );
    $step5 = str_replace("{JOIN_CLAUSES}", $join_clauses, $step4 );
    if (!file_put_contents("app/".$tablename."-read.php", $step5, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating $tablename Read file<br>";
}

function generate_delete($tablename, $column_id){

    // Load template
    $template = "templates/entity-delete.php";
    if (file_exists($template)) {
        // Read the file's content into a variable
        $deletefile = file_get_contents($template);
    } else {
        exit("File $template does not exist.");
    }

    global $CSS_REFS;
    global $JS_REFS;

    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $deletefile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);

    $step0 = str_replace("{TABLE_NAME}", $tablename, $prestep2);
    $step1 = str_replace("{TABLE_ID}", $column_id, $step0);
    if (!file_put_contents("app/".$tablename."-delete.php", $step1, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating $tablename Delete file<br><br>";
}

function generate_create($tablename,$create_records, $create_err_records, $create_sqlcolumns, $column_id, $create_numberofparams, $create_sql_params, $create_html, $create_postvars) {

    // Load template
    $template = "templates/entity-create.php";
    if (file_exists($template)) {
        // Read the file's content into a variable
        $createfile = file_get_contents($template);
    } else {
        exit("File $template does not exist.");
    }


    global $CSS_REFS;
    global $JS_REFS;

    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $createfile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);

    $step0 = str_replace("{TABLE_NAME}", $tablename, $prestep2);
    $step1 = str_replace("{CREATE_RECORDS}", $create_records, $step0);
    $step2 = str_replace("{CREATE_ERR_RECORDS}", $create_err_records, $step1);
    $step3 = str_replace("{CREATE_COLUMN_NAMES}", $create_sqlcolumns, $step2);
    $step4 = str_replace("{CREATE_QUESTIONMARK_PARAMS}", $create_numberofparams, $step3);
    $step5 = str_replace("{CREATE_SQL_PARAMS}", $create_sql_params, $step4 );
    $step6 = str_replace("{CREATE_HTML}", $create_html, $step5);
    $step7 = str_replace("{CREATE_POST_VARIABLES}", $create_postvars, $step6);
    $step8 = str_replace("{COLUMN_ID}", $column_id, $step7);
    if (!file_put_contents("app/".$tablename."-create.php", $step8, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating $tablename Create file<br>";
}

function generate_update($tablename, $create_records, $create_err_records, $create_postvars, $column_id, $create_html, $update_sql_params, $update_sql_id, $update_column_rows, $update_sql_columns){

    // Load template
    $template = "templates/entity-update.php";
    if (file_exists($template)) {
        // Read the file's content into a variable
        $updatefile = file_get_contents($template);
    } else {
        exit("File $template does not exist.");
    }

    global $CSS_REFS;
    global $JS_REFS;

    $prestep1 = str_replace("{CSS_REFS}", $CSS_REFS, $updatefile);
    $prestep2 = str_replace("{JS_REFS}", $JS_REFS, $prestep1);

    $step0 = str_replace("{TABLE_NAME}", $tablename, $prestep2);
    $step1 = str_replace("{CREATE_RECORDS}", $create_records, $step0);
    $step2 = str_replace("{CREATE_ERR_RECORDS}", $create_err_records, $step1);
    $step3 = str_replace("{COLUMN_ID}", $column_id, $step2);
    $step4 = str_replace("{UPDATE_SQL_PARAMS}", $update_sql_params, $step3);
    $step5 = str_replace("{UPDATE_SQL_ID}", $update_sql_id, $step4 );
    $step6 = str_replace("{CREATE_HTML}", $create_html, $step5);
    $step7 = str_replace("{CREATE_POST_VARIABLES}", $create_postvars, $step6);
    $step8 = str_replace("{UPDATE_COLUMN_ROWS}", $update_column_rows, $step7);
    $step9 = str_replace("{UPDATE_SQL_COLUMNS}", $update_sql_columns, $step8);
    if (!file_put_contents("app/".$tablename."-update.php", $step9, LOCK_EX)) {
        die("Unable to open file!");
    }
    echo "Generating $tablename Update file<br>";
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

function generate($postdata) {
    // echo "<pre>";
    // print_r($postdata);
    // echo "</pre>";

    // Generate app/config-tables-columns.php
    $tables_and_columns_names_preconfig = extractTableAndColumnsNames($postdata);



    // Go trough the POST array
    // Every table is a key
    global $excluded_keys;

    // An indexed array of table names to display based on their system name
    // Do not mix this variable with $tables_and_columns_names_preconfig!!!
    $tables_and_columns_names = [];

    // Array with structure $preview_columns[TABLE_NAME] where each instance contains an array of columns that
    // are selected to be include in previews, such as select foreign keys and foreign key preview.
    $preview_columns = array();
    foreach ($postdata as $key => $value) {

        if (!in_array($key, $excluded_keys)) {
            $tables_and_columns_names[extractTableName($key)]['name'] = $value[0]['tabledisplay'];
            $tables_and_columns_names[extractTableName($key)]['columns'] = array();
            foreach ($_POST[$key] as $columns) {
                if (isset($columns['columninpreview'])) {
                    $preview_columns[$columns['tablename']][] = $columns['columnname'];
                }
            }
        }
    }

    foreach ($postdata as $key => $value) {
        $tables = array();
        $tablename = '';
        $tabledisplay = '';
        $columnname = '';
        $columndisplay = '';
        $columnvisible = '';
        $columns_available = array();
        $index_sql_search = array();
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

        $join_columns = '';
        $join_clauses = '';

        global $sort;
        global $link;
        global $forced_deletion;

        if (!in_array($key, $excluded_keys)) {
            $i = 0;
            $j = 0;
            $max = count_index_colums($key) + 1;
            $total_columns = count($_POST[$key]);
            $total_params = count($_POST[$key]);
            $tablename = $_POST[$key][0]['tablename'];

            // Find foreign key references to this table
            $foreign_key_references = "";
            $sql_get_fk_ref = "SELECT
                                i.TABLE_NAME as 'Table', k.COLUMN_NAME as 'Column',
                                k.REFERENCED_TABLE_NAME as 'FK Table', k.REFERENCED_COLUMN_NAME as 'FK Column',
                                i.CONSTRAINT_NAME as 'Constraint Name'
                                FROM information_schema.TABLE_CONSTRAINTS i
                                LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                                WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND k.REFERENCED_TABLE_NAME = '$tablename'";
            $result = mysqli_query($link, $sql_get_fk_ref);
            if (mysqli_num_rows($result) > 0) {
                while ( $row = mysqli_fetch_assoc($result) ) {
                    $table = $row["Table"];
                    $fk_column = $row["FK Column"];
                    $column = $row["Column"];
                    $foreign_key_references .= '
                    $id = is_numeric($row["'.$fk_column.'"]) ? $row["'.$fk_column.'"] : "\'".$row["'.$fk_column.'"]."\'";
                    $sql = "SELECT COUNT(*) AS count FROM `'. $table .'` WHERE `'. $column .'` = ". $id . ";";
                    $number_of_refs = mysqli_fetch_assoc(mysqli_query($link, $sql))["count"];
                    if ($number_of_refs > 0)
                    {
                        $html .= \'<p><a href="' . $table . '-index.php?' . $column . '=\'. $row["' . $fk_column . '"]' . '.\'" class="btn btn-info">\' . translate("references_view_btn", false, $number_of_refs, "' . $table . '", "' . $column . '", $row["' . $fk_column . '"]) .\'</a></p></p>\';
                    }';
                }
            }
            $foreign_key_references = $foreign_key_references != "" ? '$html = "";' . $foreign_key_references . 'if ($html != "") {echo "<h3>" . translate("references_tables", false, "' . $tablename . '") . "</h3>" . $html;}' : "";

            //Specific INDEX page variables
            $column_id = null;
            foreach ($_POST[$key] as $columns) {
                if (isset($columns['primary']) && !empty($columns['primary']) && !is_null($columns['primary'])) {
                    $column_id = $columns['primary'];
                }
                else {
                    $column_id = null;
                }

                // These variables contain the generated names, labels, input field and values for column.
                // They are used at the end of this loop to create the {RECORDS_READ_FORM} and the {CREATE_HTML}
                // $columndisplay contains the name of the column
                $column_value = "";
                $column_input = "";

                $type = column_type($columns['columntype']);


                //INDEXFILE VARIABLES
                //Get the columns visible in the index file
                if (isset($columns['columnvisible'])) {
                    $column_visible = $columns['columnvisible'];
                    if ($columns['columnvisible'] == 1 && $i < $max) {

                        $columnname = $columns['columnname'];

                        if (!empty($columns['columndisplay'])) {
                            $columndisplay = $columns['columndisplay'];
                        }
                        else {
                            $columndisplay = $columns['columnname'];
                        }

                        if (!empty($columns['columncomment'])) {
                            $columndisplay_th = '";'."\n".'$str = <<<EOD'."\n";
                            $columndisplay_th .= "\t" . $columns['columncomment'] . "\n";
                            $columndisplay_th .= 'EOD;'."\n";
                            $columndisplay_th .= '?>'."\n";
                            $columndisplay_th .= '<span data-toggle="tooltip" data-placement="top" title="<?php echo str_replace(\'"\', "\'\'", $str) ?>">' . $columndisplay . '</span><?php echo "'."\n";
                        } else {
                            $columndisplay_th = $columndisplay;
                        }

                        $columns_available[] = "$columnname";
                        $index_sql_search[] = "`$tablename`.`$columnname`";

                        $index_table_headers .= "\t\t\t\t\t\t\t\t\t" . '$columnname = "' . $columnname . '";' . "\n";
                        $index_table_headers .= "\t\t\t\t\t\t\t\t\t" . '$sort_link = isset($_GET["order"]) && $_GET["order"] == $columnname && $_GET["sort"] == "asc" ? "desc" : "asc";' . "\n";
                        $index_table_headers .= "\t\t\t\t\t\t\t\t\t" . '$sort_link = isset($_GET["order"]) && $_GET["order"] == $columnname && $_GET["sort"] == "desc" ? "asc" : $sort_link;' . "\n";
                        $index_table_headers .= "\t\t\t\t\t\t\t\t\t" . 'echo "<th><a href=?search=$search&order=' . $columnname . '&sort=".$sort_link.">';
                        $index_table_headers .= $columndisplay_th;
                        $index_table_headers .= '</a></th>";' . "\n";

                        // Display date in locale format
                        if (!empty($columns['fk'])) {
                            //Get the Foreign Key
                            $tablename = $columns['tablename'];
                            $columnname = $columns['columnname'];
                            $sql_getfk = "SELECT
                                            i.TABLE_NAME as 'Table', k.COLUMN_NAME as 'Column',
                                            k.REFERENCED_TABLE_NAME as 'FK Table', k.REFERENCED_COLUMN_NAME as 'FK Column',
                                            i.CONSTRAINT_NAME as 'Constraint Name'
                                            FROM information_schema.TABLE_CONSTRAINTS i
                                            LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                                            WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND k.TABLE_NAME = '$tablename' AND k.COLUMN_NAME = '$columnname'";
                            $result = mysqli_query($link, $sql_getfk);
                            if (mysqli_num_rows($result) > 0) {
                                while ( $row = mysqli_fetch_assoc($result) ) {
                                    $fk_table = $row["FK Table"];
                                    $fk_column = $row["FK Column"];
                                }
                                $join_column_name = $columnname . $fk_table . $fk_column;
                                $is_primary_ref = is_primary_key($fk_table, $fk_column);
                                $index_table_rows .= 'echo "<td>" . get_fk_url($row["' . $columnname . '"], "' . $fk_table . '", "' . $fk_column . '", $row["' . $join_column_name . '"], ' . $is_primary_ref . ', true) . "</td>";' . "\n\t\t\t\t\t\t\t\t\t\t\t";
                            }
                        }
                        else if ($type == 1 || $type == 3 || $type == 9) // Text
                        {
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . 'echo "<td>";' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '// Check if the column is file upload' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . "// echo '<pre>';" . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '// print_r($tables_and_columns_names[' . "'" . $tablename . "'" . ']["columns"][' . "'" . $columnname . "'" . ']);' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . "// echo '</pre>';" . "\n";

                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '$has_link_file = isset($tables_and_columns_names[' . "'" . $tablename . "'" . ']["columns"][' . "'" . $columnname . "'" . '][\'is_file\']) ? true : false;' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . 'if ($has_link_file){' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '    $is_file = $tables_and_columns_names[' . "'" . $tablename . "'" . ']["columns"][' . "'" . $columnname . "'" . '][\'is_file\'];' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '    $link_file = $is_file ? \'<a href="uploads/\'. htmlspecialchars($row[' . "'" . $columnname . "'" . ']) .\'" target="_blank" class="uploaded_file" id="link_' . $columnname . '">\' : \'\';' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '    echo $link_file;' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '}' . "\n";

                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . 'echo nl2br(htmlspecialchars($row[' . "'" . $columnname . "'" . '] ?? ""));' . "\n";

                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . 'if ($has_link_file){' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '    echo $is_file ? "</a>" : "";' . "\n";
                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . '}' . "\n";

                            $index_table_rows .= "\t\t\t\t\t\t\t\t\t\t\t" . 'echo "</td>"."\n\t\t\t\t\t\t\t\t\t\t\t\t";';
                        }
                        else if ($type == 4) // TinyInt / Bool
                        {
                            $index_table_rows .= 'echo "<td>" . convert_bool($row[' . "'" . $columnname . "'" . ']) . "</td>";' . "\n\t\t\t\t\t\t\t\t\t\t\t\t";
                        }
                        else if ($type == 7) // Date
                        {
                            $index_table_rows .= 'echo "<td>" . convert_date($row[' . "'" . $columnname . "'" . ']) . "</td>";' . "\n\t\t\t\t\t\t\t\t\t\t";
                        }
                        else if ($type == 8) // Datetime
                        {
                            $index_table_rows .= 'echo "<td>" . convert_datetime($row[' . "'" . $columnname . "'" . ']) . "</td>";' . "\n\t\t\t\t\t\t\t\t\t\t";
                        }
                        else {
                            $index_table_rows .= 'echo "<td>" . htmlspecialchars($row[' . "'" . $columnname . "'" . '] ?? "") . "</td>";' . "\n\t\t\t\t\t\t\t\t\t\t";
                        }
                        $i++;
                    }
                }
            }

            //DETAIL CREATE UPDATE AND DELETE pages variables
            foreach ($_POST[$key] as $columns) {
                if ($j < $total_columns) {

                    $type = column_type($columns['columntype']);

                    if (isset($columns['columndisplay'])) {
                        $columndisplay = $columns['columndisplay'];
                    }
                    if (empty($columns['columndisplay'])) {
                        $columndisplay = $columns['columnname'];
                    }

                    if (!$columns['columnnullable']) {
                        $columndisplay .= "*";
                    }

                    if (!empty($columns['columncomment'])) {
                        $columndisplay = $columndisplay . '<small class="form-text text-muted">' . $columns['columncomment'] . '</small>';
                    }

                    if (!empty($columns['auto'])) {
                        //Dont create html input field for auto-increment columns
                        $j++;
                        $total_params--;
                    }

                    //Get all tablenames in an array
                    $tablename = $columns['tablename'];
                    if (!in_array($tablename, $tables)) {
                        $tables[$tablename] = $tabledisplay;
                    }

                    $tablename = $columns['tablename'];
                    if (!empty($columns['tabledisplay'])) {
                        $tabledisplay = $columns['tabledisplay'];
                    }
                    else {
                        $tabledisplay = $columns['tablename'];
                    }


                    if (empty($columns['auto'])) {

                        $columnname = $columns['columnname'];
                        $columnname_var = preg_replace('/[^a-zA-Z0-9]+/', '_', $columnname);

                        $create_records .= "\$$columnname_var = \"\";\n";
                        $create_record = "\$$columnname_var";
                        $create_err_records .= "\$$columnname_var" . '_err' . " = \"\";\n";
                        $create_err_record = "\$$columnname_var" . '_err';
                        $create_sqlcolumns[] = "`$columnname`";
                        $create_sql_params[] = "\$$columnname_var";

                        // Process POST vars that can be null differently
                        if ($columns['columnnullable']) {
                            $create_postvars .= "$$columnname_var = \$_POST[\"$columnname\"] == \"\" ? null : trim(\$_POST[\"$columnname\"]);\n\t\t";
                        }
                        else {
                            $create_postvars .= "$$columnname_var = trim(\$_POST[\"$columnname\"]);\n\t\t";
                        }

                        $update_sql_params[] = "`$columnname`" . '=?';
                        $update_sql_id = "`$column_id`" . '=?';
                        $update_column_rows .= "$$columnname_var = htmlspecialchars(\$row[\"$columnname\"] ?? \"\");\n\t\t\t\t\t";


                        //Foreign Key
                        //Check if there are foreign keys to take into consideration
                        if (!empty($columns['fk'])) {
                            //Get the Foreign Key
                            $sql_getfk = "SELECT
                                            i.TABLE_NAME as 'Table', k.COLUMN_NAME as 'Column',
                                            k.REFERENCED_TABLE_NAME as 'FK Table', k.REFERENCED_COLUMN_NAME as 'FK Column',
                                            i.CONSTRAINT_NAME as 'Constraint Name'
                                            FROM information_schema.TABLE_CONSTRAINTS i
                                            LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                                            WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND k.TABLE_NAME = '$tablename' AND k.COLUMN_NAME = '$columnname'";
                            $result = mysqli_query($link, $sql_getfk);
                            if (mysqli_num_rows($result) > 0) {
                                while ( $row = mysqli_fetch_assoc($result) ) {
                                    $fk_table = $row["FK Table"];
                                    $fk_column = $row["FK Column"];
                                }


                                //Be careful code below is particular regarding single and double quotes.

                                $html = '<select class="form-control" id="' . $columnname . '" name="' . $columnname . '">';
                                if ($columns['columnnullable']) {
                                    $html .= '<option value="">Null</option>';
                                }


                                $fk_columns_select = isset($preview_columns[$fk_table]) ? get_sql_select($preview_columns[$fk_table]) : '';

                                $join_name = $columnname . $fk_table;
                                $join_column_name = $columnname . $fk_table . $fk_column;

                                $join_clauses .= "\n\t\t\tLEFT JOIN `$fk_table` AS `$join_name` ON `$join_name`.`$fk_column` = `$tablename`.`$columnname`";
                                $join_columns .= isset($preview_columns[$fk_table]) ? get_sql_concat_select($preview_columns[$fk_table], $join_name, $join_column_name) : '';

                                // Add the new columns to the search concat
                                if (isset($preview_columns[$fk_table]) && is_iterable($preview_columns[$fk_table])) {
                                    foreach ($preview_columns[$fk_table] as $key => $c) {
                                        $index_sql_search[] = '`' . $join_name . '`.`' . $preview_columns[$fk_table][$key] . '`';
                                    }
                                }

                                $is_primary_ref = is_primary_key($fk_table, $fk_column);

                                $column_value = '<?php echo get_fk_url($row["' . $columnname . '"], "' . $fk_table . '", "' . $fk_column . '", $row["' . $join_column_name . '"], ' . $is_primary_ref . ', false); ?>';

                                if ($fk_columns_select) {
                                    $html .= ' <?php
                                    $sql = "SELECT ' . $fk_columns_select . ', `' . $fk_column . '` FROM `' . $fk_table . '` ORDER BY ' . $fk_columns_select . '";
                                    $result = mysqli_query($link, $sql);
                                    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                        $duprow = $row;
                                        unset($duprow["' . $fk_column . '"]);
                                        $value = implode(" | ", $duprow);
                                        $' . $columnname_var . ' = isset($' . $columnname_var . ') ? $' . $columnname_var . ' : null;
                                        if ($row["' . $fk_column . '"] == $' . $columnname_var . '){
                                            echo \'<option value="\' . $row["' . $fk_column . '"] . \'"selected="selected">\' . $value . \'</option>\';
                                        } else {
                                            echo \'<option value="\' . $row["' . $fk_column . '"] . \'">\' . $value . \'</option>\';
                                        }
                                    }
                                    ?>
                                    </select>';
                                    $column_input = $html;
                                    unset($html);
                                }
                            }

                            // No Foreign Keys, just regular columns from here on
                        }
                        else {

                            // Display date in locale format
                            if ($type == 1) // Text
                            {
                                $column_value = '<?php echo nl2br(htmlspecialchars($row["' . $columnname . '"] ?? "")); ?>';
                            }
                            else if ($type == 4) // TinyInt / Bool
                            {
                                $column_value = '<?php echo convert_bool($row["' . $columnname . '"]); ?>';
                            }
                            else if ($type == 7) // Date
                            {
                                $column_value = '<?php echo convert_date($row["' . $columnname . '"]); ?>';
                            }
                            else if ($type == 8) // Datetime
                            {
                                $column_value = '<?php echo convert_datetime($row["' . $columnname . '"]); ?>';
                            }
                            else {
                                $column_value = '<?php echo htmlspecialchars($row["' . $columnname . '"] ?? ""); ?>';
                            }

                            //$type = column_type($columns['columntype']);


                            // Check for a specific configuration of the column
                            // echo '<pre>';
                            // print_r($tables_and_columns_names_preconfig);
                            // echo '</pre>';
                            $is_special_column_type = false;

                            // Check if the column is a file upload
                            if (isset($tables_and_columns_names_preconfig[$tablename]['columns'][$columnname]['is_file']) && $tables_and_columns_names_preconfig[$tablename]['columns'][$columnname]['is_file']) {
                                $is_special_column_type = true;
                                $column_input = "\n" . '<input type="file" name="' . $columnname . '" id="' . $columnname . '" class="form-control">' . "\n";
                                $column_input .= '<input type="hidden" name="cruddiy_backup_' . $columnname . '" id="cruddiy_backup_' . $columnname . '" value="<?php echo @' . $create_record . '; ?>">' . "\n";
                                $column_input .= '<?php if (isset(' . $create_record . ') && !empty(' . $create_record . ')) : ?>' . "\n";
                                $column_input .= '<div class="custom-control custom-checkbox">' . "\n";
                                $column_input .= '    <input type="checkbox" class="custom-control-input" id="cruddiy_delete_' . $columnname . '" name="cruddiy_delete_' . $columnname . '" value="1">' . "\n";
                                $column_input .= '    <label class="custom-control-label" for="cruddiy_delete_' . $columnname . '">'. "\n";
                                $html_link_upload = '<a href="uploads/<?php echo ' . $create_record . ' ?>" target="_blank"><?php echo ' . $create_record . ' ?></a>';
                                $column_input .= '<?php translate("Delete:") ?>: '.$html_link_upload;
                                $column_input .= '    </label>' . "\n";
                                $column_input .= '</div>' . "\n";
                                $column_input .= '<?php endif ?>' . "\n";
                            }



                            // Not an upload column
                            if (!$is_special_column_type) {
                                switch ($type) {
                                    //TEXT
                                    case 1:
                                        $column_input = '<textarea name="' . $columnname . '" id="' . $columnname . '" class="form-control"><?php echo @' . $create_record . '; ?></textarea>';
                                        break;

                                    //ENUM types
                                    case 2:
                                        //Make sure on the update form that the previously selected type is also selected from the list

                                        $html = '<select name="' . $columnname . '" class="form-control" id="' . $columnname . '">';
                                        if ($columns['columnnullable']) {
                                            $html .= '<option value="">Null</option>';
                                        }

                                        $sql_enum = "SELECT COLUMN_TYPE as AllPossibleEnumValues
                                                FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tablename' AND COLUMN_NAME = '$columnname';";
                                        $result = mysqli_query($link, $sql_enum);
                                        $row = mysqli_fetch_array($result, MYSQLI_NUM);
                                        preg_match('/enum\((.*)\)$/', $row[0], $matches);
                                        $html .= "<?php \n\t\t\t\t\t\t\t \$enum_$columnname = array(" . $matches[1] . ");";
                                        $html .= "
                                                foreach (\$enum_$columnname as " . ' $val){
                                                    if ($val == $' . $columnname . '){
                                                        echo \'<option value="\' . $val . \'" selected="selected">\' . $val . \'</option>\';
                                                    } else
                                                    echo \'<option value="\' . $val . \'">\' . $val . \'</option>\';
                                                }
                                                ?></select>';

                                        $column_input = $html;
                                        unset($html);
                                        break;
                                    //VARCHAR
                                    case 3:
                                        preg_match('#\((.*?)\)#', $columns['columntype'], $match);
                                        $maxlength = $match[1];
                                        $column_input = '<input type="text" name="' . $columnname . '" id="' . $columnname . '" maxlength="' . $maxlength . '" class="form-control" value="<?php echo @' . $create_record . '; ?>">';
                                        break;

                                    //TINYINT (bool)
                                    case 4:
                                        $regex = "/'(.*?)'/";
                                        preg_match_all($regex, $columns['columntype'], $enum_array);
                                        $html = '<select name="' . $columnname . '" id="' . $columnname . '" class="form-control" id="' . $columnname . '">';
                                        if ($columns['columnnullable']) {
                                            $html .= '<option value="">Null</option>';
                                        }
                                        $html .= '    <option value="0" <?php echo !@' . $create_record . ' ? "selected": ""; ?> >False</option>';
                                        $html .= '    <option value="1" <?php echo @' . $create_record . ' ? "selected": ""; ?> >True</option>';
                                        $html .= '</select>';
                                        $column_input = $html;
                                        unset($html);
                                        break;
                                    //INT
                                    case 5:
                                        $column_input = '<input type="number" name="' . $columnname . '" id="' . $columnname . '" class="form-control" value="<?php echo @' . $create_record . '; ?>">';
                                        break;

                                    //DECIMAL
                                    case 6:
                                        $column_input = '<input type="number" name="' . $columnname . '" id="' . $columnname . '" class="form-control" value="<?php echo @' . $create_record . '; ?>" step="any">';
                                        break;
                                    //DATE
                                    case 7:
                                        $column_input = '<input type="date" name="' . $columnname . '" id="' . $columnname . '" class="form-control" value="<?php echo @' . $create_record . '; ?>">';
                                        break;
                                    //DATETIME
                                    case 8:
                                        $column_input = '<input type="datetime-local" name="' . $columnname . '" id="' . $columnname . '" class="form-control" value="<?php echo empty(' . $create_record . ') ? "" : date("Y-m-d\TH:i:s", strtotime(@' . $create_record . ')); ?>">';
                                        break;

                                    default:
                                        $column_input = '<input type="text" name="' . $columnname . '" id="' . $columnname . '" class="form-control" value="<?php echo @' . $create_record . '; ?>">';
                                        break;
                                }
                            }
                        }

                        // Regular layout
                        $create_html[] = '<div class="form-group">
                                            <label for="' . $columnname . '">' . $columndisplay . '</label>
                                            ' . $column_input . '
                                        </div>';

                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '<?php' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '// Check if the column is file upload' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . "// echo '<pre>';" . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '// print_r($tables_and_columns_names[' . "'" . $tablename . "'" . ']["columns"][' . "'" . $columnname . "'" . ']);' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . "// echo '</pre>';" . "\n";

                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '$has_link_file = isset($tables_and_columns_names[' . "'" . $tablename . "'" . ']["columns"][' . "'" . $columnname . "'" . '][\'is_file\']) ? true : false;' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . 'if ($has_link_file){' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '    $is_file = $tables_and_columns_names[' . "'" . $tablename . "'" . ']["columns"][' . "'" . $columnname . "'" . '][\'is_file\'];' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '    $link_file = $is_file ? \'<a href="uploads/\'. htmlspecialchars($row[' . "'" . $columnname . "'" . ']) .\'" target="_blank" class="uploaded_file" id="link_' . $columnname . '">\' : \'\';' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '    $end_link_file = $is_file ? "</a>" : "";' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '}' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '?>' . "\n";

                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '<div class="form-group">' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '    <h4>' . $columndisplay . '</h4>' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '    <?php if ($has_link_file): ?>' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '        <p class="form-control-static"><?php echo $link_file ?>' . $column_value . '<?php echo $end_link_file ?></p>' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '    <?php endif ?>' . "\n";
                        $read_records .= "\t\t\t\t\t\t\t\t\t" . '</div>';


                        // Different Layout
                        // $create_html [] = '<div class="form-group row">
                        // <label class="col-sm-4 col-form-label" for="'.$columnname.'">'.$columndisplay.'</label>
                        // <div class="col-sm-7">'. $column_input .'</div></div>';
                        // $read_records .= '<div class="form-group row">
                        //     <div class="col-sm-3 font-weight-bold">'.$columndisplay.'</div>
                        //     <div class="col-sm-7">'. $column_value .'</div></div>';
                        $j++;
                    }
                }

                if ($j == $total_columns) {

                    $update_sql_columns = $create_sql_params;
                    $update_sql_columns[] = "\$$column_id";
                    $update_sql_columns = implode(", ", $update_sql_columns);

                    $index_sql_search = implode(", ", $index_sql_search);
                    $create_numberofparams = array_fill(0, $total_params, '?');
                    $create_numberofparams = implode(", ", $create_numberofparams);
                    $create_sqlcolumns = implode(", ", $create_sqlcolumns);
                    $create_sql_params = implode(", ", $create_sql_params);
                    $create_html = implode("\n\t\t\t\t\t\t", $create_html);

                    $update_sql_params = implode(",", $update_sql_params);

                    //Generate everything
                    $start_page = "";

                    foreach ($tables as $key => $value) {
                        //echo "$key is at $value";
                        //$start_page .= '<a href="'. $key . '-index.php" class="btn btn-primary" role="button">'. $value. '</a> ';
                        //$button_string = "\t".'<a class="dropdown-item" href="'.$start_page_link.'">'.$td.'</a>'."\n\t".$buttons_delimiter;
                        $start_page .= '<a href="' . $key . '-index.php" class="dropdown-item">' . $value . '</a> ';
                        $start_page .= "\n\t";
                    }

                    // force existing files deletion
                    if (!$forced_deletion && (!isset($_POST['keep_startpage']) || (isset($_POST['keep_startpage']) && $_POST['keep_startpage'] != 'true'))) {
                        $forced_deletion = true;
                        echo '<h3>Deleting existing files in app/</h3>';
                        $keep = array('config.php', 'helpers.php', 'config-tables-columns.php', 'locales');
                        foreach (glob("app/*") as $file) {
                            if (!in_array(basename($file), $keep)) {
                                if (is_file($file)) {
                                    if (unlink($file)) {
                                        echo $file . '<br>';
                                    }
                                }
                            }
                        }

                        echo '<br>';
                        global $upload_target_dir;
                        global $upload_persistent_dir;
                        echo '<h3>Deleting upload directory</h3>';
                        if (!$upload_persistent_dir) {
                            deleteDirectory("app/$upload_target_dir");
                            echo "app/$upload_target_dir was deleted (set <code>\$upload_persistent_dir=true</code> in app/config.php to preserve next time)";
                        }
                        else {
                            echo "app/$upload_target_dir was preserved due to <code>\$upload_persistent_dir=true</code> in app/config.php";
                        }

                        // always delete the locales directory as we may need to regenerate the translations
                        deleteDirectory("app/locales");
                        recursiveCopy('locales', 'app/locales');

                        echo '<br><br>';
                    }

                    generate_navbar($value, $start_page, isset($_POST['keep_startpage']) && $_POST['keep_startpage'] == 'true' ? true : false, isset($_POST['append_links']) && $_POST['append_links'] == 'true' ? true : false, $tabledisplay, $key);
                    generate_error();
                    generate_startpage();
                    generate_index($tablename, $tabledisplay, $index_table_headers, $index_table_rows, $column_id, $columns_available, $index_sql_search, $join_columns, $join_clauses);
                    generate_create($tablename, $create_records, $create_err_records, $create_sqlcolumns, $column_id, $create_numberofparams, $create_sql_params, $create_html, $create_postvars);
                    generate_read($tablename, $column_id, $read_records, $foreign_key_references, $join_columns, $join_clauses);
                    generate_update($tablename, $create_records, $create_err_records, $create_postvars, $column_id, $create_html, $update_sql_params, $update_sql_id, $update_column_rows, $update_sql_columns);
                    generate_delete($tablename, $column_id);
                }

            }

        }

    }
}



// Extract table name
function extractTableName($post_key) {
    // Find the position of the last occurrence of 'columns'
    $lastPos = strrpos($post_key, "columns");

    if ($lastPos !== false) {
        // Remove the last occurrence of 'columns'
        $table_name = substr_replace($post_key, "", $lastPos, strlen("columns"));
    } else {
        $table_name = $post_key;
    }

    return $table_name;
}



// Save table and columns configuration
function updateTableAndColumnsNames($tables_and_columns_names) {

    $configTableNamesFilePath     = 'app/config-tables-columns.php';
    $configTableNamesTemplatePath = 'templates/config-tables-columns.php';

    // Read config file template
    $configfile = fopen($configTableNamesTemplatePath, "r") or die("Unable to read Config file template for table names!");
    $templateContent = file_get_contents($configTableNamesTemplatePath);

    // Prepare the new tables array as a string
    $new_table_names = var_export($tables_and_columns_names, true);

    // Replace placeholders with actual values
    $replacements = [
        '{{TABLE_NAMES}}' => $new_table_names,
    ];

    foreach ($replacements as $placeholder => $realValue) {
        $templateContent = str_replace($placeholder, $realValue, $templateContent);
    }

    $configfile = fopen($configTableNamesFilePath, "w") or die("Unable to open Config file for table names! Please check your file permissions.");
    if (fwrite($configfile, $templateContent) === false) die("Error writing Config file for table names!");
    fclose($configfile);

    return $tables_and_columns_names;
}



/**
 * Values to be saved in config-tables-columns.php
 * so that we don't have to re-configure everything when we regenerate CRUD
 * @param mixed $postdata
 * @return void
 */
function extractTableAndColumnsNames($postdata) {

    $tables_and_columns_names = [];

    foreach($postdata as $table => $columns) {
        if (isset($columns[0]['tabledisplay'])) {
            $tables_and_columns_names[extractTableName($table)]['name'] = $columns[0]['tabledisplay'];
            foreach($columns as $column) {
                $tables_and_columns_names[extractTableName($table)]['columns'][$column['columnname']]['columndisplay'] = $column['columndisplay'] ? $column['columndisplay'] : $column['columnname'];
                $tables_and_columns_names[extractTableName($table)]['columns'][$column['columnname']]['is_file'] = isset($column['file']) && $column['file'] ? 1 : 0;
                $tables_and_columns_names[extractTableName($table)]['columns'][$column['columnname']]['columnvisible'] = isset($column['columnvisible']) && $column['columnvisible'] ? 1 : 0;
                $tables_and_columns_names[extractTableName($table)]['columns'][$column['columnname']]['columninpreview'] = isset($column['columninpreview']) && $column['columninpreview'] ? 1 : 0;
            }
        }
    }

    // Save all to a file in app/
    return updateTableAndColumnsNames($tables_and_columns_names);
}




function deleteDirectory($dirPath) {
    echo "delete : $dirPath";
    if (!is_dir($dirPath)) {
        // throw new InvalidArgumentException("$dirPath must be a directory");
        return false;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileInfo) {
        if ($fileInfo->isDir()) {
            rmdir($fileInfo->getRealPath());
        } else {
            unlink($fileInfo->getRealPath());
        }
    }

    rmdir($dirPath);
}


function recursiveCopy($src, $dst) {
    // Create the destination directory if it doesn't exist
    if (!file_exists($dst)) {
        mkdir($dst, 0777, true);
    }

    // Open the source directory
    $dir = opendir($src);

    // Loop through the files in the source directory
    while (($file = readdir($dir)) !== false) {
        if (($file != '.') && ($file != '..')) {
            // If it's a directory, recursively copy it
            if (is_dir($src . '/' . $file)) {
                recursiveCopy($src . '/' . $file, $dst . '/' . $file);
            }
            // If it's a file, copy it
            else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }

    // Close the source directory
    closedir($dir);
}


?>
<!doctype html>
<html lang="en">
<head>
    <title>Generated pages</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body class="bg-light">
<section class="py-5">
    <div class="container bg-white py-5 shadow">
        <div class="row">
            <div class="col-md-12 mx-auto px-5">
                <?php generate($_POST); ?>
                <hr>
                <br>Your app has been created! It is completely self contained in the /app folder. You can move this folder anywhere on your server.<br><br>
                <a href="app/index.php" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg">Go to your app &nbsp; <i class="fa fa-external-link" aria-hidden="true"></i></a><br><br>
                You can close this tab or leave it open and use the back button to make changes and regenerate the app. Every run will overwrite the previous app unless you checked the "Keep previously generated startpage" box.<br><br>
                <hr>
                If you need further instructions please visit <a href="http://cruddiy.com" target="_blank">cruddiy.com</a> or ask on our <a href="https://github.com/jan-vandenberg/cruddiy" target="_blank">GitHub</a> project.

            </div>
        </div>
    </div>
</section>
<script src="https://kit.fontawesome.com/6b773fe9e4.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
