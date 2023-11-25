<?php

$db_server              = '{{db_server}}';
$db_name                = '{{db_name}}';
$db_user                = '{{db_user}}';
$db_password            = '{{db_password}}';
$no_of_records_per_page = '{{no_of_records_per_page}}';
$appname                = '{{appname}}';

$protocol               = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$domain                 = $protocol . '://' . $_SERVER['SCRIPT_NAME']; // Replace domain with your domain name. (Locally typically something like localhost)

$link                   = mysqli_connect($db_server, $db_user, $db_password, $db_name);

$query = "SHOW VARIABLES LIKE 'character_set_database'";
if ($result = mysqli_query($link, $query)) {
    while ($row = mysqli_fetch_row($result)) {
        if (!$link->set_charset($row[1])) {
            printf("Error loading character set %s: %s\n", $row[1], $link->error);
            exit();
        } else {
            // printf("Current character set: %s", $link->character_set_name());
        }
    }
}

?>
