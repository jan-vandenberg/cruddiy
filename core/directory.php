<?php
session_start();
include 'helpers.php';

$configDirectories = getConfigDirectories(__DIR__);

// Display a message if no config.php files are found
if (empty($configDirectories)) {
    // echo "No configuration files found. Please ensure config.php exists in the subdirectories.";
    header('location:index.php?generator=new');
    exit();
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['configDir'])) {
    $_SESSION['destination'] = basename($_POST['configDir']);
    if (isset($_GET['from']) && !empty($_GET['from'])) {
        header('location:' . $_GET['from'] . '.php');
    } else {
        header('location:index.php');
    }
    exit();
}
?><!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Select existing config</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <style type="text/css">
        table tr td:last-child a {
            margin-right: 15px;
        }
    </style>
</head>
<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto">

            <!-- Form Name -->
            <div class="text-center pt-5">
                <h4>Select existing app</h4>
            </div>

            <?php if (isset($_GET["empty"])) : ?>
                <div class="alert alert-danger" role="alert">
                    The field <?php echo $_GET["empty"]; ?> cannot be empty.
                </div>
            <?php endif ?>

            <form class="form-group-row" method="post">
                <fieldset>

                    <div class="form-group">
                        <p>
                            You have already run the Cruddiy generator.<br>
                            Do you want to re-use an existing configuration?
                        </p>

                        <label for="configDir">Select an app:</label>
                        <br>
                        <select name="configDir" id="configDir">
                            <?php if (!empty($configDirectories)): ?>
                                <?php foreach ($configDirectories as $dir): ?>
                                    <option value="<?php echo htmlspecialchars($dir); ?>"><?php echo htmlspecialchars($dir); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option>No configuration files found</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group d-flex justify-content-between">
                        <input type="submit" class="btn btn-primary" value="Load Configuration">
                        <a class="btn btn-secondary" href="index.php?generator=new">Restart from scratch</a>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>