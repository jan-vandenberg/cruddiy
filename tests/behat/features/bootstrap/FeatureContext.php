<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Testwork\Tester\Result\TestResult;
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
     * @BeforeScenario @resetDB
     */
    public function resetDB(BeforeScenarioScope $scope) {

        $this->deleteTestUploads($scope);

        // Delete the test database and let the test suite re-create it
        try {
            $this->pdo->exec("DROP DATABASE IF EXISTS `" . $_ENV['DB_BASE'] . "`");
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $_ENV['DB_BASE'] . "`  COLLATE '" . $_ENV['DB_CHAR'] . "'");
            $this->pdo->exec("GRANT ALL ON `" . $_ENV['DB_BASE'] . "`.* TO '" . $_ENV['DB_USER'] . "'@'localhost'");
            $this->pdo->exec("FLUSH PRIVILEGES");
            // $this->showDatabases();
        } catch (PDOException $e) {
            // Handle the exception
            throw new \RuntimeException('Database creation failed: ' . $e->getMessage());
        }
    }



    /**
     * @BeforeScenario @cleanTestRecordsAndUploads
     */
    public function cleanTestRecordsAndUploads(BeforeScenarioScope $scope) {
        $this->cleanDB($scope);
        $this->deleteTestUploads($scope);
    }



    // Delete files uploaded by the Public test suite
    private function deleteTestUploads(BeforeScenarioScope $scope) {
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
    private function cleanDB(BeforeScenarioScope $scope) {
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



    /**
     * @Then /^I should see "([^"]*)" only once$/
     */
    public function iShouldSeeOnlyOnce($text) {
        $page = $this->getSession()->getPage();
        $allText = $page->getText();
        $occurrences = substr_count($allText, $text);

        if ($occurrences !== 1) {
            throw new ExpectationException("Expected to find the text '$text' only once, found $occurrences times.", $this->getSession()->getDriver());
        }
    }



    /**
     * @BeforeSuite
     */
    public static function cleanLogDirectory() {
        // Cleanup old logs when a test suite runs
        // see logPageContent() for logging.
        $logDirectory = __DIR__ . '/../../logs';

        if (file_exists($logDirectory)) {
            $files = glob($logDirectory . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep') {
                    unlink($file);
                }
            }
        }
    }




    /**
     * @AfterStep
     */
    public function logFailedStep(AfterStepScope $scope) {
        if ($scope->getTestResult()->getResultCode() !== TestResult::PASSED) {
            $this->logPageContent($scope);
        }
    }

    // Log HTML page if the step has not passed
    public function logPageContent($scope) {
        $feature = $scope->getFeature()->getFile();
        $line = $scope->getStep()->getLine();

        $logDirectory = __DIR__ . '/../../logs';
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        // Identify the directory separator and build the features string accordingly
        $dirSeparator = DIRECTORY_SEPARATOR;
        $featuresString = $dirSeparator . "features" . $dirSeparator;
        $featuresPos = strpos($feature, $featuresString) + strlen($featuresString);
        $relativePath = substr($feature, $featuresPos);

        // Replace directory separators with underscores and remove the .feature extension
        $filename = str_replace($dirSeparator, '_', $relativePath);
        $filename = basename($filename, '.feature');

        // Construct the log filename
        $logFilename = $logDirectory . '/' . $filename . '-' . $line . '.html';

        // Save the page content to the log file
        file_put_contents($logFilename, $this->getSession()->getPage()->getContent());

        // Display the log filename in test results
        echo "HTML content logged to: " . $logFilename . PHP_EOL;
    }








}
