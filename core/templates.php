<?php

$errorfile = <<<'EOT'
<?php
require_once('config.php');
require_once('helpers.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php translate('Error') ?></title>
    {CSS_REFS}
</head>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1><?php translate('Invalid Request') ?></h1>
                    </div>
                    <div class="alert alert-danger fade-in">
                        <p><?php translate('invalid_request_instructions') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {JS_REFS}
</body>
</html>
EOT;

$startfile = <<<'EOT'
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{APP_NAME}</title>
    {CSS_REFS}
    {JS_REFS}

    <style type="text/css">
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 5px;
        }
    </style>
</head>
<?php require_once('config.php'); ?>
<?php require_once('helpers.php'); ?>
<?php require_once('navbar.php'); ?>
</html>
EOT;

$navbarfile = <<<'EOT'
<?php require_once('config-tables-columns.php'); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand nav-link" href="index.php">{APP_NAME}</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php translate('Select Page') ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        {TABLE_BUTTONS}
        <!-- TABLE_BUTTONS -->
        </div>
      </li>
    </ul>
  </div>
</nav>
EOT;


