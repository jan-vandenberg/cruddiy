<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{APP_NAME}</title>
    {CSS_REFS}
    <script src="https://kit.fontawesome.com/6b773fe9e4.js" crossorigin="anonymous"></script>
    <style type="text/css">
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 5px;
        }
        body {
            font-size: 14px;
        }
    </style>
</head>
<?php require_once('config.php'); ?>
<?php require_once('config-tables-columns.php'); ?>
<?php require_once('helpers.php'); ?>
<?php require_once('navbar.php'); ?>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <?php
                        // Prevent crash if $str contains single quotes
                        $str = <<<'EOD'
                        {TABLE_DISPLAY}
                        EOD;
                        ?>
                        <h2 class="float-left"><?php translate('%s Details', true, $str) ?></h2>
                        <a href="{TABLE_NAME}-create.php" class="btn btn-success float-right"><?php translate('Add New Record') ?></a>
                        <a href="{TABLE_NAME}-index.php" class="btn btn-info float-right mr-2"><?php translate('Reset View') ?></a>
                        <a href="javascript:history.back()" class="btn btn-secondary float-right mr-2"><?php translate('Back') ?></a>
                    </div>

                    <div class="form-row">
                        <form action="{TABLE_NAME}-index.php" method="get">
                        <div class="col">
                          <input type="text" class="form-control" placeholder="<?php translate('Search this table') ?>" name="search">
                        </div>
                    </div>
                        </form>
                    <br>

                    <?php
                    //Get current URL and parameters for correct pagination
                    $script   = $_SERVER['SCRIPT_NAME'];
                    $parameters   = $_GET ? $_SERVER['QUERY_STRING'] : "" ;
                    $currenturl = $domain. $script . '?' . $parameters;

                    //Pagination
                    if (isset($_GET['pageno'])) {
                        $pageno = $_GET['pageno'];
                    } else {
                        $pageno = 1;
                    }

                    //$no_of_records_per_page is set on the index page. Default is 10.
                    $offset = ($pageno-1) * $no_of_records_per_page;

                    $total_pages_sql = "SELECT COUNT(*) FROM `{TABLE_NAME}`";
                    $result = mysqli_query($link,$total_pages_sql);
                    $total_rows = mysqli_fetch_array($result)[0];
                    $total_pages = ceil($total_rows / $no_of_records_per_page);

                    //Column sorting on column name
                    $columns = array('{COLUMNS}');
                    // Order by primary key on default
                    $order = '{COLUMN_ID}';
                    if (isset($_GET['order']) && in_array($_GET['order'], $columns)) {
                        $order = $_GET['order'];
                    }

                    //Column sort order
                    $sortBy = array('asc', 'desc'); $sort = 'asc';
                    if (isset($_GET['sort']) && in_array($_GET['sort'], $sortBy)) {
                          if($_GET['sort']=='asc') {
                            $sort='asc';
                            }
                    else {
                        $sort='desc';
                        }
                    }

                    //Generate WHERE statements for param
                    $where_columns = array_intersect_key($_GET, array_flip($columns));
                    $get_param = "";
                    $where_statement = " WHERE 1=1 ";
                    foreach ( $where_columns as $key => $val ) {
                        $where_statement .= " AND `$key` = '" . mysqli_real_escape_string($link, $val) . "' ";
                        $get_param .= "&$key=$val";
                    }

                    if (!empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($link, $_GET['search']);
                        if (strpos('{INDEX_CONCAT_SEARCH_FIELDS}', ',')) {
                            $where_statement .= " AND CONCAT_WS ({INDEX_CONCAT_SEARCH_FIELDS}) LIKE '%$search%'";
                        } else {
                            $where_statement .= " AND {INDEX_CONCAT_SEARCH_FIELDS} LIKE '%$search%'";
                        }

                    } else {
                        $search = "";
                    }

                    $order_clause = !empty($order) ? "ORDER BY `$order` $sort" : '';
                    $group_clause = !empty($order) && $order == '{COLUMN_ID}' ? "GROUP BY `{TABLE_NAME}`.`$order`" : '';

                    // Prepare SQL queries
                    $sql = "SELECT `{TABLE_NAME}`.* {JOIN_COLUMNS}
                            FROM `{TABLE_NAME}` {JOIN_CLAUSES}
                            $where_statement
                            $group_clause
                            $order_clause
                            LIMIT $offset, $no_of_records_per_page;";
                    $count_pages = "SELECT COUNT(*) AS count FROM `{TABLE_NAME}` {JOIN_CLAUSES}
                            $where_statement";

                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            $number_of_results = mysqli_fetch_assoc(mysqli_query($link, $count_pages))['count'];
                            $total_pages = ceil($number_of_results / $no_of_records_per_page);
                            translate('total_results', true, $number_of_results, $pageno, $total_pages);
                            ?>

                            <table class='table table-bordered table-striped'>
                                <thead class='thead-light'>
                                    <tr>
                                        <?php {INDEX_TABLE_HEADERS} ?>
                                        <th><?php translate('Actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_array($result)): ?>
                                        <tr>
                                            <?php {INDEX_TABLE_ROWS} ?>
                                            <td>
                                                <?php
                                                $column_id = '{COLUMN_ID}';
                                                if (!empty($column_id)): ?>
                                                    <a id='read-<?php echo $row['{COLUMN_NAME}']; ?>' href='{TABLE_NAME}-read.php?{COLUMN_ID}=<?php echo $row['{COLUMN_NAME}']; ?>' title='<?php echo addslashes(translate('View Record', false)); ?>' data-toggle='tooltip' class='btn btn-sm btn-info'><i class='far fa-eye'></i></a>
                                                    <a id='update-<?php echo $row['{COLUMN_NAME}']; ?>' href='{TABLE_NAME}-update.php?{COLUMN_ID}=<?php echo $row['{COLUMN_NAME}']; ?>' title='<?php echo addslashes(translate('Update Record', false)); ?>' data-toggle='tooltip' class='btn btn-sm btn-warning'><i class='far fa-edit'></i></a>
                                                    <a id='delete-<?php echo $row['{COLUMN_NAME}']; ?>' href='{TABLE_NAME}-delete.php?{COLUMN_ID}=<?php echo $row['{COLUMN_NAME}']; ?>' title='<?php echo addslashes(translate('Delete Record', false)); ?>' data-toggle='tooltip' class='btn btn-sm btn-danger'><i class='far fa-trash-alt'></i></a>
                                                <?php else: ?>
                                                    <?php echo addslashes(translate('unsupported_no_pk')); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>




                                <ul class="pagination" align-right>
                                <?php
                                    $new_url = preg_replace('/&?pageno=[^&]*/', '', $currenturl);
                                 ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $new_url .'&pageno=1' ?>"><?php translate('First') ?></a></li>
                                    <li class="page-item <?php if($pageno <= 1){ echo 'disabled'; } ?>">
                                        <a class="page-link" href="<?php if($pageno <= 1){ echo '#'; } else { echo $new_url ."&pageno=".($pageno - 1); } ?>"><?php translate('Prev') ?></a>
                                    </li>
                                    <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                                        <a class="page-link" href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo $new_url . "&pageno=".($pageno + 1); } ?>"><?php translate('Next') ?></a>
                                    </li>
                                    <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                                        <a class="page-item"><a class="page-link" href="<?php echo $new_url .'&pageno=' . $total_pages; ?>"><?php translate('Last') ?></a>
                                    </li>
                                </ul>
<?php
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>" . translate('No records were found.') . "</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }

                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>
        </div>
    </section>
{JS_REFS}
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
