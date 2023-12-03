<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;



class AdminFeatureContext extends FeatureContext implements Context {

    /**
     * @BeforeScenario @deconfigure
    */
    public function reset(BeforeScenarioScope $scope) {
        $this->resetDatabase();
        $this->deleteTablesColumnsConfig();
        $this->deleteTestUploads();
    }


    public function resetDatabase() {
        // Delete the test database and let the test suite re-create it
        try {
            $this->pdo->exec("DROP DATABASE IF EXISTS `" . $_ENV['DB_BASE'] . "`");
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $_ENV['DB_BASE'] . "`  COLLATE '" . $_ENV['DB_CHAR'] . "'");
            $this->pdo->exec("GRANT ALL ON `" . $_ENV['DB_BASE'] . "`.* TO '" . $_ENV['DB_USER'] . "'@'localhost'");
            $this->pdo->exec("FLUSH PRIVILEGES");
            // self::showDatabases();
        } catch (PDOException $e) {
            // Handle the exception
            throw new \RuntimeException('Database creation failed: ' . $e->getMessage());
        }
    }



    public function deleteTablesColumnsConfig() {
        $directory = __DIR__ . '/../../../../core/app/';
        $files = array('config.php', 'config-tables-columns.php');
        foreach ($files as $file) {
            if (file_exists($directory . $file)) {
                unlink($directory . $file);
            }
        }
    }



}
