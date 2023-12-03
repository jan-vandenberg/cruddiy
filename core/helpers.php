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