<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;



class PublicFeatureContext extends FeatureContext implements Context {

    /**
     * @BeforeScenario @reconfigure
    */
    public function reconfigure(BeforeScenarioScope $scope) {
        $this->deleteTestUploads();
        $this->importPublicDatabase();
        $this->importTablesColumnsConfig();
    }


    public function importTablesColumnsConfig() {
        $root = __DIR__ . '/../../../..';
        $sourceFile = $root . '/tests/templates/config-tables-columns.php';
        $destinationFile = $root . '/core/app/config-tables-columns.php';

        // Copy the file
        if (!copy($sourceFile, $destinationFile)) {
            throw new \RuntimeException('File ' . $sourceFile . 'could not be copied.');
        }
    }



    // Delete the test database and re-import what was created by the Admin test suite
    // This is useful to run the Public test suite multiple times, whithout encounting errors
    // because the Public test suites performs operations that invalidates the tests.
    private function importPublicDatabase() {
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

            $this->pdo->exec("DROP DATABASE IF EXISTS `" . $_ENV['DB_BASE'] . "`");
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $_ENV['DB_BASE'] . "`  COLLATE '" . $_ENV['DB_CHAR'] . "'");
            $this->pdo->exec("GRANT ALL ON `" . $_ENV['DB_BASE'] . "`.* TO '" . $_ENV['DB_USER'] . "'@'localhost'");
            $this->pdo->exec("FLUSH PRIVILEGES");
            $this->pdo->exec("USE `" . $_ENV['DB_BASE'] . "`;\n". $dump_contents);
            // self::showDatabases();
        } catch (PDOException $e) {
            // Handle the exception
            throw new \RuntimeException('Database creation failed: ' . $e->getMessage());
        }
    }

}
