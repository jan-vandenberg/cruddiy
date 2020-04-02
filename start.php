<?php
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connect to database</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
</head>
<form class="form-horizontal" action="tables.php" method="post" >
<fieldset>

<!-- Form Name -->
<center>
<legend>Enter database information</legend>
</center>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Server</label>  
  <div class="col-md-4">
  <input id="server" name="server" type="text" placeholder="localhost" class="form-control input-md">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Database</label>  
  <div class="col-md-4">
  <input id="database" name="database" type="text" placeholder="" class="form-control input-md">
  </div>
</div>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Username</label>  
  <div class="col-md-4">
  <input id="username" name="username" type="text" placeholder="" class="form-control input-md">
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordinput">Password</label>
  <div class="col-md-4">
    <input id="password" name="password" type="password" placeholder="" class="form-control input-md">
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Submit</button>
  </div>
</div>
</fieldset>
</form>
