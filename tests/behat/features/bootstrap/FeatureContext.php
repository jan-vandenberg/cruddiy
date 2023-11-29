<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ExpectationException;
use Dotenv\Dotenv;

class FeatureContext extends MinkContext implements Context {

    private $pdo;
    private $dotenv;

    /**
     * Initializes context.
     *
     * @param array $parameters Context parameters (set them in behat.yml)
     */
    public function __construct(array $parameters = []) {
        $this->dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
        $this->dotenv->load();

        try {
            // Connect to MySQL
            // TODO: Cruddiy generator does not support port variable
            $this->pdo = new PDO('mysql:host=' . $_ENV['DB_HOST']. ';port=' . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
            // $this->showDatabases();
        } catch (PDOException $e) {
            // Handle the exception
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }



    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope) {

        try {
            // Create database
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $_ENV['DB_BASE'] . "`  COLLATE '" . $_ENV['DB_CHAR'] . "'");
            $this->pdo->exec("GRANT ALL ON `" . $_ENV['DB_BASE'] . "`.* TO '" . $_ENV['DB_USER'] . "'@'localhost'");
            $this->pdo->exec("FLUSH PRIVILEGES");
            // $this->showDatabases();
        } catch (PDOException $e) {
            // Handle the exception
            throw new \RuntimeException('Database creation failed: ' . $e->getMessage());
        }
    }



    public function showDatabases() {
        $sql = 'SHOW DATABASES';
        $stmt = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $row) {
            print $row."\n";
        }
    }



    /**
     * @Given I fill in :field with the environment variable :envVariable
     */
    public function iFillInWithEnvironmentVariable($field, $envVariable) {
        $envValue = $_ENV[$envVariable] ?? null;

        if ($envValue === null) {
            throw new \Exception("Environment variable '$envVariable' is not set.");
        }

        $this->fillField($field, $envValue);
    }
}
