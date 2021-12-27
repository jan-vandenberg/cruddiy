    <?php
        include "app/config.php";
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>CRUD generator</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    </head>
    <body class="bg-light">
    <section class="pt-5">
        <div class="container bg-white shadow py-5">
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
                            $res = mysqli_query($link,"SHOW TABLES");
                            while($cRow = mysqli_fetch_array($res))
                            {
                                $tablelist[] = $cRow[0];
                            }

                            //Loop trough list of tables
                            $i = 0;
                            foreach($tablelist as $table) {

                                echo
                        '<div class="row align-items-center">
                            <div class="col-md-3 text-right">
                                  <label class="control-label" for="table['.$i.'][tablename]">'. $table . ' </label>
                            </div>
                            <div class="col-md-6">
                                     <input type="hidden" name="table['.$i.'][tablename]" value="'.$table.'"/>
                                     <input id="textinput_'.$table. '" name="table['.$i.'][tabledisplay]" type="text" placeholder="Display table name in frontend" class="form-control rounded-0 shadow-sm">
                            </div>
                            <div class="col-md-3">
                              <input class="mr-1" type="checkbox"  name="table['.$i.'][tablecheckbox]" id="checkboxes-'.$i.'" value="1"><label for="checkboxes-'.$i.'">Generate CRUD</label>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script>
    $(document).ready(function () {
        $('#checkall').click(function(e) {
            var chb = $('.form-horizontal').find('input[type="checkbox"]');
            chb.prop('checked', !chb.prop('checked'));
        });
    });
    </script>
    </body>
    </html>
