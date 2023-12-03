<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;



class PublicFeatureContext extends FeatureContext implements Context {


    /**
     * @BeforeSuite
     */
    public static function BeforeSuite(BeforeSuiteScope $scope) {
        self::deleteTestUploads($scope);
        // self::cleanDB($scope);
    }



    // Delete files uploaded by the Public test suite
    private static function deleteTestUploads(BeforeScenarioScope $scope) {
        $directory = __DIR__ . '/../../../../core/app/uploads';
        $substring = '_cruddiy_test_image.jpg';

        foreach (glob($directory . '/*') as $file) {
            if (is_file($file) && strpos(basename($file), $substring) !== false) {
                unlink($file);
            }
        }
    }



    // Delete the test database and re-import what was created by the Admin test suite
    // This is useful to run the Public test suite multiple times, whithout encounting errors
    // because the Public test suites performs operations that invalidates the tests.
    private static function cleanDB(BeforeScenarioScope $scope) {
        try {
            $dir_schema = realpath(__DIR__ . '/../../../../schema');
            $file_schema = 'Tests - Public.sql';

            // Use realpath() to get the absolute path of the file
            $dump_file = realpath($dir_schema . '/' . $file_schema);

            if ($dump_file === false) {
                die("Schema file not found for Public tests!");
            }

            // Now read the contents of the file
            $dump_contents = file_get_contents($dump_file);
            if ($dump_contents === false) {
                die("Unable to read schema for Public tests!");
            }
            // print_r($dump_contents);

            self::pdo->exec("DROP DATABASE IF EXISTS `" . $_ENV['DB_BASE'] . "`");
            self::pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $_ENV['DB_BASE'] . "`  COLLATE '" . $_ENV['DB_CHAR'] . "'");
            self::pdo->exec("GRANT ALL ON `" . $_ENV['DB_BASE'] . "`.* TO '" . $_ENV['DB_USER'] . "'@'localhost'");
            self::pdo->exec("FLUSH PRIVILEGES");
            self::pdo->exec("USE `" . $_ENV['DB_BASE'] . "`;\n". $dump_contents);
            // self::showDatabases();
        } catch (PDOException $e) {
            // Handle the exception
            throw new \RuntimeException('Database creation failed: ' . $e->getMessage());
        }
    }



}
