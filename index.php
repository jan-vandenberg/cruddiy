<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connect to database</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <style type="text/css">
                table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
</head>
<div class="container">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <form class="form-group-row" action="relations.php" method="post" >
                <fieldset>

                    <!-- Form Name -->
                    <div class="text-center pt-5">
                        <h4>Enter database information</h4>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-form-label" for="textinput">Server</label>
                            <input id="server" name="server" type="text" placeholder="localhost" class="form-control ">
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-form-label" for="textinput">Database</label>
                            <input id="database" name="database" type="text" placeholder="" class="form-control input-md">
                    </div>
                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-form-label" for="textinput">Username</label>
                            <input id="username" name="username" type="text" placeholder="" class="form-control input-md">
                    </div>

                    <!-- Password input-->
                    <div class="form-group">
                        <label class="col-form-label" for="passwordinput">Password</label>
                            <input id="password" name="password" type="password" placeholder="" class="form-control input-md">
                    </div>

                    <!-- Number records per page-->
                    <div class="form-group">
                            <label class="col-form-label" for="recordsperpage">Items per generated page</label>
                            <input id="numrecordsperpage" name="numrecordsperpage" type="number" min="1" max="1000" placeholder="Number of items per page" class="form-control input-md" value="10">
                    </div>


                    <div class="form-group">
                        <label class="col-form-label" for="index"></label>
                            <button id="index" name="index" class="btn btn-primary">Submit</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
