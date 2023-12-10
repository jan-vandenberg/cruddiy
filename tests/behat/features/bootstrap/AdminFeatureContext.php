<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;



class AdminFeatureContext extends FeatureContext implements Context {

    private $gitignoreContent;

    /**
     * @BeforeScenario @deconfigure
    */
    public function reset(BeforeScenarioScope $scope) {
        $this->resetDatabase();
        $this->deleteTablesColumnsConfig();
        $this->deleteTestUploads();
        $this->removeDestinationFromGitignore();
    }



    public function removeDestinationFromGitignore() {
        $gitignorePath = __DIR__ . '/../../../../.gitignore';
        $stringToRemove = "app_cruddiy_tests/";

        // Read the file into an array. Each line becomes an array element
        $lines = file($gitignorePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Check if the file was read successfully
        if ($lines === false) {
            die("Error reading the .gitignore file.");
        }

        // Remove the line containing the specific string
        $lines = array_filter($lines, function($line) use ($stringToRemove) {
            return trim($line) !== $stringToRemove;
        });

        // Write the remaining lines back to the file
        file_put_contents($gitignorePath, implode("\n", $lines));

        echo "Removed $stringToRemove from .gitignore file.";
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
        $directory = __DIR__ . '/../../../../core/app_cruddiy_tests/';
        $files = array('config.php', 'config-tables-columns.php');
        foreach ($files as $file) {
            if (file_exists($directory . $file)) {
                unlink($directory . $file);
            }
        }
    }



    /**
     * @Given I have a .gitignore file
     */
    public function iHaveAGitignoreFile()
    {
        // Adjust the path to your .gitignore file
        $gitignorePath = __DIR__ . '/../../../../.gitignore';
        if (!file_exists($gitignorePath)) {
            throw new Exception(".gitignore file does not exist in $gitignorePath");
        }
        $this->gitignoreContent = file_get_contents($gitignorePath);
    }

    /**
     * @When I check for :subdirectory in the .gitignore file
     */
    public function iCheckForInTheGitignoreFile($subdirectory)
    {
        if (strpos($this->gitignoreContent, $subdirectory) === false) {
            throw new Exception("Subdirectory $subdirectory not found in .gitignore.");
        }
    }

    /**
     * @When I check for no :subdirectory in the .gitignore file
     */
    public function iCheckForNoInTheGitignoreFile($subdirectory)
    {
        if (!strpos($this->gitignoreContent, $subdirectory) === false) {
            throw new Exception("Subdirectory $subdirectory was found in .gitignore.");
        }
    }

}
