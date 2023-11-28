<?php
include "app/config.php";

function get_primary_key($table){
    global $link;
    $sql = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
    $result = mysqli_query($link,$sql);
    $primary_key = null;
    while($row = mysqli_fetch_assoc($result))
    {
        $primary_key = $row['Column_name'];
    }
    return $primary_key;
}

function get_autoincrement_cols($table){
    global $link;
    $sql = "DESCRIBE $table";
    $result = mysqli_query($link,$sql);
    $auto_keys = Array();
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
}

function get_col_comments($table,$column){
    global $link;
    $sql = "SHOW FULL FIELDS FROM $table where FIELD ="."'".$column."'";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    return $row['Comment'] ;
}

function get_col_nullable($table,$column){
    global $link;
    $sql = "SHOW FULL FIELDS FROM $table where FIELD ="."'".$column."'";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    return ($row['Null'] == "YES") ? true : 0;
}

function get_foreign_keys($table){
    global $link;
    global $db_name;
    $fks[] = "";
    $sql = "SELECT k.COLUMN_NAME as 'Foreign Key'
            FROM information_schema.TABLE_CONSTRAINTS i
            LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
            WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND i.TABLE_NAME = '$table'";
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result))
    {
        $fks[] = $row['Foreign Key'];
    }
    return $fks;
}

$tablesData = [];
$checked_tables_counter=0;

if (isset($_POST['table'])) {
    foreach ($_POST['table'] as $table) {
        if (isset($table['tablecheckbox']) && $table['tablecheckbox'] == 1) {
            $checked_tables_counter++;

            $tableName    = $table['tablename'];
            $tableDisplay = $table['tabledisplay'];
            $primaryKey  = get_primary_key($tableName);
            $autoKeys     = get_autoincrement_cols($tableName);
            $foreignKeys  = get_foreign_keys($tableName);

            $sql          = "SHOW columns FROM $tableName";
            $result       = mysqli_query($link, $sql);
            $columns      = [];

            while ($column = mysqli_fetch_array($result)) {
                $columns[] = [
                    'type'         => get_col_types($tableName, $column[0]),
                    'comment'      => get_col_comments($tableName, $column[0]),
                    'nullable'     => get_col_nullable($tableName, $column[0]),
                    'name'         => $column[0],
                    'isPrimary'    => $primaryKey,
                    'isAuto'       => in_array($column[0], $autoKeys),
                    'isForeignKey' => in_array($column[0], $foreignKeys),
                ];
            }

            $tablesData[] = [
                'name'    => $tableName,
                'display' => $tableDisplay,
                'columns' => $columns,
            ];
        }
    }
}

?><!doctype html>
<html lang="en">
<head>
    <title>Select Columns</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body class="bg-light">
<section class="py-5">
    <div class="container bg-white shadow py-5">
        <div class="row">
            <div class="col-md-12 mx-auto">

                <div class="row">
                    <div class="col-12 text-center">
                        <h4 class="h1 border-bottom pb-2">All Available Columns</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-2">
                        <input type="checkbox" id="checkall-1" checked>
                        <label for="checkall-1">Check/uncheck all</label>
                    </div>
                    <div class="col-2">
                        <input type="checkbox" id="checkall-2" checked>
                        <label for="checkall-2">Check/uncheck all</label>
                    </div>
                </div>

                <form class="form-horizontal" action="generate.php" method="post">
                    <fieldset>

                        <?php foreach ($tablesData as $table): ?>
                            <?php
                            // echo '<pre>';
                            // print_r($table);
                            // echo '</pre>';
                            ?>
                            <div class="row">
                                <div class="col-3"></div>
                                <div class="col-9 my-4">
                                    <?php
                                    $configTableNamesFilePath = 'app/config-tables-columns.php';
                                    if (file_exists($configTableNamesFilePath)) {
                                        include($configTableNamesFilePath);
                                    }
                                    ?>
                                    <strong>Table: <?= htmlspecialchars($table['display']) ?> (<?= htmlspecialchars($table['name']) ?>)</strong>
                                </div>
                            </div>

                            <?php foreach ($table['columns'] as $i => $column): ?>
                                <div class="row align-items-center mb-2">
                                    <div class="col-3 text-right">
                                        <label class="col-form-label" for="<?= htmlspecialchars($table['name']) . '-' . $i ?>">
                                            <?= htmlspecialchars($column['name']) ?>
                                            <?= $column['isPrimary'] ? '🔑' : '' ?>
                                            <?= $column['isAuto'] ? '🔒' : '' ?>
                                            <?= $column['isForeignKey'] ? '🛅' : '' ?>
                                            <?= $column['nullable'] ? '🫙' : '' ?>
                                        </label>
                                    </div>
                                    <div class="col-md-5">
                                        <?php
                                        // echo '<pre>';
                                        // print_r($table);
                                        // print_r($column['name']);
                                        // echo '</pre>';
                                        ?>

                                        <!-- Hidden inputs and text input for column display name -->
                                        <?php if ($column['isForeignKey']) : ?>
                                            <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][fk]" value="1"/>
                                        <?php endif ?>

                                        <?php if ($column['isPrimary']) : ?>
                                            <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][primary]" value="<?php echo $column['isPrimary'] ?>"/>
                                        <?php endif ?>

                                        <?php if ($column['isAuto']) : ?>
                                            <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][auto]" value="1"/>
                                        <?php endif ?>

                                        <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][tablename]" value="<?= htmlspecialchars($table['name']) ?>"/>
                                        <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][tabledisplay]" value="<?= htmlspecialchars($table['display']) ?>"/>

                                        <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columnname]" value="<?= htmlspecialchars($column['name']) ?>"/>
                                        <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columntype]" value="<?= htmlspecialchars($column['type']) ?>"/>
                                        <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columncomment]" value="<?= htmlspecialchars($column['comment']) ?>"/>
                                        <input type="hidden" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columnnullable]" value="<?php echo $column['nullable'] ?>"/>

                                        <input id="textinput_<?= htmlspecialchars($table['name']) . '-' . $i ?>"
                                                name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columndisplay]"
                                                type="text"
                                                placeholder="Display field name in frontend"
                                                class="form-control rounded-0"
                                                <?php echo isset($tables_columns_names[$table['name']]['columns'][$column['name']]) ? 'value="'.addslashes(htmlspecialchars($tables_columns_names[$table['name']]['columns'][$column['name']])).'"' : '' ?>
                                                >
                                    </div>
                                    <div class="col-md-2">
                                        <!-- Visible in overview checkbox -->
                                        <input type="checkbox" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columnvisible]" id="checkboxes-<?= $i ?>" value="1" checked>
                                        <label for="checkboxes-<?= $i ?>">Visible in overview?</label>
                                    </div>
                                    <div class="col-md-2">
                                        <!-- Visible in preview checkbox -->
                                        <input type="checkbox" name="<?= htmlspecialchars($table['name']) ?>columns[<?= $i ?>][columninpreview]" id="checkboxes-<?= $i ?>-2" value="1" checked>
                                        <label for="checkboxes-<?= $i ?>-2">Visible in preview?</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <br>
                        <?php endforeach; ?>


                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <p class="form-check">
                                    <small id="passwordHelpBlock" class="form-text text-muted">
                                        Cruddiy will create a fresh startpage in the app/ sub-folder, with link<?php echo $checked_tables_counter > 1 ? 's' : '' ?> to manage the table<?php echo $checked_tables_counter > 1 ? 's' : '' ?> above.<br>
                                        If you have used Cruddiy on other tables before, your start page will be replaced by the fresh one, and previous links will be lost.
                                    </small>
                                    <input class="form-check-input" type="checkbox" value="true" id="keep_startpage" name="keep_startpage">
                                    <label class="form-check-label" for="keep_startpage">
                                        Keep previously generated startpage and CRUD pages if they exist
                                    </label>
                                    <br>
                                    <input class="form-check-input" type="checkbox" value="true" id="append_links" name="append_links">
                                    <label class="form-check-label" for="append_links">
                                        Append new link<?php echo $checked_tables_counter > 1 ? 's' : '' ?> to previously generated startpage if necessary
                                    </label>
                                </p>
                            </div>
                            <div class="col-md-8 mx-auto">
                                <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-success btn-block rounded-0 shadow-sm">Generate Pages</button>
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
<script>
$(document).ready(function () {
    $('#checkall-1').click(function(e) {
        var chb = $('.form-horizontal').find('input[name$="[columnvisible]"]');
        chb.prop('checked', !chb.prop('checked'));
    });
});
$(document).ready(function () {
    $('#checkall-2').click(function(e) {
        var chb = $('.form-horizontal').find('input[name$="[columninpreview]"]');
        chb.prop('checked', !chb.prop('checked'));
    });
});
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>