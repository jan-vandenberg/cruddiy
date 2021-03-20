<?php
function parse_columns($table_name, $postdata) {
    global $link;
    $vars = array();

    echo "<pre>";
    print_r($postdata);

    // prepare a default return value
    $default = null;

    // get all columns, including the ones not sent by the CRUD form
    $sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '".$table_name."'";
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result))
    {
        switch($row['DATA_TYPE']) {

            // fix "Incorrect decimal value: '' error in STRICT_MODE or STRICT_TRANS_TABLE 
            // @see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html
            case 'decimal':
                $default = 0;
                break;

            // fix "Incorrect datetime value: '0' " on non-null datetime columns
            // with 'CURRENT_TIMESTAMP' default not being set automatically
            // and refusing to take NULL value
            case 'datetime':
                $default = ($row['COLUMN_DEFAULT'] != 'CURRENT_TIMESTAMP' && $row['IS_NULLABLE'] == 'YES') ? null : date('Y-m-d H:i:s');
                break;
        }
        
        // check that fieldname was set before sending values to pdo
        $vars[$row['COLUMN_NAME']] = isset($_POST[$row['COLUMN_NAME']]) && $_POST[$row['COLUMN_NAME']] ? trim($_POST[$row['COLUMN_NAME']]) : $default;
    }
    
    // /*
    // debug
    echo "<pre>";
    print_r($postdata);
    echo $row['COLUMN_NAME'] . "\t";
    echo $row['DATA_TYPE'] . "\t";
    echo $row['IS_NULLABLE'] . "\t";
    echo $row['COLUMN_DEFAULT'] . "\t";
    echo $row['EXTRA'] . "\t";
    echo $default . "\n";
    echo "</pre>";
    // */
    
    return $vars;
}
?>