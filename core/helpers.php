<?php
// retrieves and enhances postdata table keys and values on CREATE and UPDATE events
function parse_columns($table_name, $postdata)
{
    global $link;
    $vars = array();

    // prepare a default return value
    $default = null;

    // get all columns, including the ones not sent by the CRUD form
    $sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '" . $table_name . "'";
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {

        $debug = 0;
        if ($debug) {
            echo "<pre>";
            // print_r($postdata);
            echo $row['COLUMN_NAME'] . "\t";
            echo $row['DATA_TYPE'] . "\t";
            echo $row['IS_NULLABLE'] . "\t";
            echo $row['COLUMN_DEFAULT'] . "\t";
            echo $row['EXTRA'] . "\t";
            echo $default . "\n";
            echo "</pre>";
        }

        switch ($row['DATA_TYPE']) {

            // fix "Incorrect decimal value: '' error in STRICT_MODE or STRICT_TRANS_TABLE
            // @see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html
            case 'decimal':
                $default = 0;
                break;

            // fix "Incorrect datetime value: '0' " on non-null datetime columns
            // with 'CURRENT_TIMESTAMP' default not being set automatically
            // and refusing to take NULL value
            case 'datetime':
                if ($row['COLUMN_DEFAULT'] != 'CURRENT_TIMESTAMP' && $row['IS_NULLABLE'] == 'YES') {
                    $default = null;
                } else {
                    $default = date('Y-m-d H:i:s');
                }
                if ($postdata[$row['COLUMN_NAME']] == 'CURRENT_TIMESTAMP') {
                    $_POST[$row['COLUMN_NAME']] = date('Y-m-d H:i:s');
                }
                break;
        }

        // check that fieldname was set before sending values to pdo
        $vars[$row['COLUMN_NAME']] = isset($_POST[$row['COLUMN_NAME']]) && $_POST[$row['COLUMN_NAME']] ? trim($_POST[$row['COLUMN_NAME']]) : $default;
    }
    return $vars;
}



// get extra attributes for  table keys on CREATE and UPDATE events
function get_columns_attributes($table_name, $column)
{
    global $link;
    $sql = "SELECT COLUMN_DEFAULT, COLUMN_COMMENT
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '" . $table_name . "'
            AND column_name = '" . $column . "'";
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $debug = 0;
        if ($debug) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        }
        return $row;
    }
}

function print_error_if_exists($error)
{
    if (isset($error)) {
        if (!is_array($error)) {
            echo "<div class='alert alert-danger' role='alert'>$error</div>";
        } else {
            foreach ($error as $err) {
                echo "<div class='alert alert-danger' role='alert'>$err</div>";
            }
        }
    }
}

function convert_date($date_str)
{
    if (isset($date_str)) {
        $date = date('d-m-Y', strtotime($date_str));
        return htmlspecialchars($date);
    }
}

function convert_datetime($date_str)
{
    if (isset($date_str)) {
        $date = date('d-m-Y H:i:s', strtotime($date_str));
        return htmlspecialchars($date);
    }
}

function convert_bool($var)
{
    if (isset($var)) {
        return $var ? "True" : "False";
    }
}

function get_fk_url($value, $fk_table, $fk_column, $representation, bool $pk=false, bool $index=false)
// Gets a URL to the foreign key parents read page
{
    if (isset($value)) {
        $value = htmlspecialchars($value);
        if($pk)
        {
            return '<a href="' . $fk_table . '-read.php?' . $fk_column . '=' . $value . '">' . $representation . '</a>';
        }
        else
        {
            return '<a href="' . $fk_table . '-index.php?' . $fk_column . '=' . $value . '">' . $representation . '</a>';
        }
    }
}

function translate($key, $echo = true, ...$args)
{
    global $translations;

    // Check if the key exists in the array
    if (isset($translations[$key])) {
        if ($echo) {
            echo sprintf($translations[$key], ...$args);
        } else {
            return sprintf($translations[$key], ...$args);
        }
    } else {
        // echo key itself if translation not found
        if ($echo) {
            echo $key;
        } else {
            return $key;
        }
    }
}




function handleFileUpload($FILE) {

    global $upload_max_size;
    global $upload_target_dir;
    global $upload_disallowed_exts;

    $upload_results     = array();
    $sanitized_fileName = sanitize(basename($FILE["name"]));
    $unique_filename    = generateUniqueFileName($sanitized_fileName);
    $target_file        = $upload_target_dir . $unique_filename;
    $extension          = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the upload directory exists
    if (!file_exists($upload_target_dir)) {
        // The 0777 permission will be modified by your umask
        mkdir($upload_target_dir, 0777, true);

        // Write a dummy index file to prevent directory listing
        file_put_contents($upload_target_dir . '/index.php', '');
        // $upload_results['error'] = "Upload directory created.";
    } else {
        // $upload_results['error'] = "Upload directory already exists.";
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $upload_results['error'] = "Sorry, the file " . htmlspecialchars(basename($FILE["name"])) . " already exists.";
        return $upload_results;
    }

    // Check file size (example: 5MB limit)
    if ($FILE["size"] > $upload_max_size) {
        $upload_results['error'] = "Sorry, the file " . htmlspecialchars(basename($FILE["name"])) . " is too large.";
        return $upload_results;
    }

    // Extensions blacklist
    if (in_array($extension, $upload_disallowed_exts)) {
        $upload_results['error'] = "Sorry, uploading files with extension $extension is not allowed.";
        return $upload_results;
    }

    // Try to upload file
    if (empty($upload_results)) {
        if (move_uploaded_file($FILE["tmp_name"], $target_file)) {
            $upload_results['success'] = $unique_filename;
        } else {
            $upload_results['error'] = "Sorry, there was an error uploading the file " . htmlspecialchars(basename($FILE["name"])) . ".";
        }
    }
    return $upload_results;
}



function sanitize($fileName) {
    // Remove illegal file system characters
    $fileName = str_replace(array('<', '>', ':', '"', '/', '\\', '|', '?', '*'), '', $fileName);

    // Normalize Unicode characters
    if (class_exists('Normalizer')) {
        $fileName = Normalizer::normalize($fileName, Normalizer::FORM_C);
    }

    // Replace spaces with underscores
    $fileName = str_replace(' ', '_', $fileName);

    // Convert to lowercase for consistency
    $fileName = strtolower($fileName);

    // Truncate to a maximum length to avoid system limitations (255 characters is a safe bet)
    $fileName = substr($fileName, 0, 255);

    return $fileName;
}



function sanitizePath($path) {
    // Split the path into segments
    $parts = explode('/', $path);

    // Sanitize each part of the path
    foreach ($parts as &$part) {
        // Skip special segments that reference the current or parent directory
        if ($part === '.' || $part === '..') {
            continue;
        }

        // Apply the same sanitization as in your original function
        $part = str_replace(array('<', '>', ':', '"', '|', '?', '*'), '', $part);

        // Normalize Unicode characters
        if (class_exists('Normalizer')) {
            $part = Normalizer::normalize($part, Normalizer::FORM_C);
        }

        // Replace spaces with underscores and convert to lowercase
        $part = strtolower(str_replace(' ', '_', $part));

        // Truncate to a maximum length
        $part = substr($part, 0, 255);
    }

    // Reassemble the path
    return implode('/', $parts);
}



function generateUniqueFileName($originalFileName) {
    $timestamp = time();
    $salt = uniqid(); // Alternatively, use bin2hex(random_bytes(8)) for more randomness
    $uniquePrefix = $timestamp . '_' . $salt . '_';

    return $uniquePrefix . $originalFileName;
}



function getUploadResultByErrorCode($code) {
    // https://www.php.net/manual/en/features.file-upload.errors.php
    $phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );
    return $phpFileUploadErrors[$code];
}



function truncate($string, $length = 15) {
    // Decode HTML entities to ensure they are not cut in the middle
    $decodedString = html_entity_decode($string);

    // Check if the string needs to be truncated
    if (mb_strlen($decodedString) > $length) {
        // Truncate the string and encode HTML entities
        $truncated = htmlspecialchars(mb_substr($decodedString, 0, $length)) . '...';
    } else {
        // No need to truncate, just encode HTML entities
        $truncated = htmlspecialchars($string);
    }

    return $truncated;
}



function findConfigFile() {

}



// Scan directories and find config.php
function getConfigDirectories($baseDir, $excludedDirs = ['locales', 'templates']) {
    $dirs = array_filter(glob($baseDir . '/*', GLOB_ONLYDIR), function ($dir) use ($excludedDirs) {
        return !in_array(basename($dir), $excludedDirs);
    });

    $configDirs = [];
    foreach ($dirs as $dir) {
        if (file_exists($dir . '/config.php')) {
            $configDirs[] = basename($dir);
        }
    }

    return $configDirs;
}



function exportAsCSV($data, $db_name, $tables_and_columns_names, $table_name, $link, $debug = false) {

    // Find relations in table
    $relations = get_foreign_key_relations($link, $db_name);

    // Generate headers
    $headers = extract_csv_headers($relations, $tables_and_columns_names, $table_name, $debug);

    // Generate rows
    $lines = [];
    foreach($data as $row) {
        $lines[] = extract_csv_data($row, $relations, $db_name, $tables_and_columns_names, $table_name, $link, $debug);
    }

    if ($debug) {
        echo "<pre>";
        print_r($headers);
        print_r($lines);
        echo "</pre>";
    }

    // Assemble the CSV
    create_csv($headers, $lines, $table_name, $debug);
}



function create_csv($headers, $lines, $table_name, $debug = false) {

    // Set appropriate headers for output
    if (!$debug) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $table_name . '_export.csv"');
    }

    // Open output stream for writing
    $output = fopen($debug ? 'php://output' : 'php://temp', 'w+');

    // Write CSV headers
    fputcsv($output, array_values($headers));

    // Iterate through the data and write to CSV
    foreach($lines as $line) {
        fputcsv($output, $line);
    }

    // Finalize CSV output for download
    if (!$debug) {
        rewind($output);
        fpassthru($output);
        fclose($output);
    }
}



function extract_csv_data($row, $relations, $db_name, $tables_and_columns_names, $table_name, $link, $debug = false) {
    // Init headers
    $line = [];

    // Parse config-tables-columns.php to get the table display settings
    foreach ($tables_and_columns_names[$table_name]['columns'] as $column_name => $column_config) {
        // Add column if it's visible
        if ($column_config['columnvisible']) {

            // The column value in this row
            $value = $row[$column_name];

            // Check if the column is a foreign key
            $relation = find_relation_by_column($relations, $column_name);

            // The column is a FK
            if ($relation !== NULL) {
                if ($debug) {
                    // Name of the column that is a foreign key
                    echo "<br>FK <strong>".$relation['table'].'.'.$relation['column']. '</strong> : '.$tables_and_columns_names[$relation['table']]['columns'][$relation['column']]['columndisplay'];
                }

                $primary = find_primary_key_from_config($table_name, $tables_and_columns_names);

                if ($debug) {
                    echo "<br>&nbsp;value: " . $value;
                }

                if ($value !== null) {
                    // Get data from related table
                    $related_value = get_related_table_data($link, $relation['table'], $primary, $value, $debug);
                } else {
                    // Keep the null value for the CSV row
                    $related_value = null;
                }

                // Look for the columns in the foreign table that are displayed in the preview
                foreach($tables_and_columns_names[$relation['table']]['columns'] as $related_column => $related_column_config) {
                    if ($related_column_config['columninpreview']) {

                        if ($related_column_config['columninpreview']) {

                            if ($debug) {
                                if ($value !== null) {
                                    echo "<br>&nbsp;related value: " . $related_value[$related_column];
                                } else {
                                    echo "<br>&nbsp;related value: <code>null</code>";
                                }
                            }

                            if ($value !== null) {
                                $line[] = $related_value[$related_column];
                            } else {
                                $line[] = null;
                            }
                        }
                    }
                }

            }
            // The column is not a FK
            else {
                if ($debug) {
                    echo "<br>column <code>$column_name</code> (".$value.')';
                }
                $line[] = $value;
            }
        }
    }

    if ($debug) {
        echo "<hr>";
    }

    return $line;
}



function get_related_table_data($link, $referenced_table_name, $primary, $value, $debug = false) {

    $sql = "
        SELECT *
        FROM   `$referenced_table_name`
        WHERE  `$referenced_table_name`.`$primary` = $value
    ";

    if ($debug) {
        echo '<pre>';
        print_r($sql);
        echo '</pre>';
    }

    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row;
}



function extract_csv_headers($relations, $tables_and_columns_names, $table_name, $debug = false) {
    // Init headers
    $headers = [];

    // Parse config-tables-columns.php to get the table display settings
    foreach ($tables_and_columns_names[$table_name]['columns'] as $column => $column_config) {
        // Add column if it's visible
        if ($column_config['columnvisible']) {

            // Check if the column is a foreign key
            $relation = find_relation_by_column($relations, $column);

            // The column is a FK
            if ($relation !== NULL) {
                if ($debug) {
                    // Name of the column that is a foreign key
                    echo "<br>FK <strong>".$relation['table'].'.'.$relation['column']. '</strong> : '.$tables_and_columns_names[$relation['table']]['columns'][$relation['column']]['columndisplay'];
                }

                // Look for the columns in the foreign table that are displayed in the preview
                foreach($tables_and_columns_names[$relation['table']]['columns'] as $related_column => $related_column_config) {
                    if ($related_column_config['columninpreview']) {

                        if ($related_column_config['columninpreview']) {

                            if ($column_config['columndisplay'] == $related_column_config['columndisplay']) {
                                $title = $related_column_config['columndisplay'];
                            } else {
                                $title = $column_config['columndisplay'] . ' - ' . $related_column_config['columndisplay'];
                            }

                            if ($debug) {
                                echo "<br>&nbsp;related <code>".$related_column. '</code> ('.$title.')';
                            }
                            $headers[] =  $title;
                        }
                    }
                }
            }
            // The column is not a FK
            else {
                if ($debug) {
                    echo "<br>column <code>$column</code> (".$column_config['columndisplay'].')';
                }
                $headers[] = $column_config['columndisplay'];
            }
        }
    }
    return $headers;
}



function get_foreign_key_relations($link, $db_name) {
    $sql = "
        SELECT
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = '$db_name'
    ";
    $result = mysqli_query($link, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $rows;
}



function find_relation_by_column($relations, $column_name) {
    foreach ($relations as $relation) {
        if ($relation['COLUMN_NAME'] === $column_name) {
            return [
                'table' => $relation['REFERENCED_TABLE_NAME'],
                'column' => $relation['REFERENCED_COLUMN_NAME'],
            ];
        }
    }
    return NULL;
}



function find_primary_key_from_config($table_name, $tables_and_columns_names) {
    foreach ($tables_and_columns_names[$table_name]['columns'] as $column_name => $column_config) {
        if ($column_config['primary']) {
            return $column_name;
        }
    }
}