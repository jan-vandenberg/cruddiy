    <?php
    include "app/config.php";
    ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
        <title>CRUD generator</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet">

    </head>

    <body>
        <section class="pt-5">
            <div class="container shadow py-5">
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div class="text-center mb-4">
                            <h4 class="h1 border-bottom pb-2">All Available Tables</h4>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col-md-11 text-right pr-5 ml-3">
                                <input type="checkbox" id="checkall">
                                <label for="checkall">Check/uncheck all</label>
                            </div>
                        </div>


                        <form class="form-horizontal" action="columns.php" method="post">
                            <fieldset>
                                <?php
                                //Get all tables
                                $tablelist = array();
                                $res = mysqli_query($link, "SHOW TABLES");
                                while ($cRow = mysqli_fetch_array($res)) {
                                    $tablelist[] = $cRow[0];
                                }

                                //Loop trough list of tables
                                $i = 0;
                                foreach ($tablelist as $table) {

                                    echo
                                    '<div class="row align-items-center">
                            <div class="col-md-3 text-right">
                                  <label class="control-label" for="table[' . $i . '][tablename]">' . $table . ' </label>
                            </div>
                            <div class="col-md-6">
                                     <input type="hidden" name="table[' . $i . '][tablename]" value="' . $table . '"/>
                                     <input id="textinput_' . $table . '" name="table[' . $i . '][tabledisplay]" type="text" placeholder="Display table name in frontend" class="form-control rounded-0 shadow-sm">
                            </div>
                            <div class="col-md-3">
                              <input class="mr-1" type="checkbox"  name="table[' . $i . '][tablecheckbox]" id="checkboxes-' . $i . '" value="1"><label for="checkboxes-' . $i . '">Generate CRUD</label>
                            </div>
                        </div>
                        ';

                                    $i++;
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-6 mx-auto">
                                        <label class="control-label" for="singlebutton"></label>
                                        <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-success btn-block shadow rounded-0">Select columns from tables</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <br>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                $('#checkall').click(function(e) {
                    var chb = $('.form-horizontal').find('input[type="checkbox"]');
                    chb.prop('checked', !chb.prop('checked'));
                });
            });
        </script>
    </body>

    </html>