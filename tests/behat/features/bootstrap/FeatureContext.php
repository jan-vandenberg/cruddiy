<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ExpectationException;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Tester\Result\TestResult;
use Dotenv\Dotenv;



class FeatureContext extends MinkContext implements Context {

    protected $pdo;
    protected $dotenv;

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
     * @BeforeSuite
     */
    public static function BeforeSuite(BeforeSuiteScope $scope) {
        self::emptyLogDirectory($scope);
    }



    public static function emptyLogDirectory(BeforeSuiteScope $scope) {
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
        } else {
            // echo "$envVariable: $envValue";
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




    // Delete files uploaded by the Public test suite
    public function deleteTestUploads() {
        $directory = __DIR__ . '/../../../../core/app/uploads';
        $substring = '_cruddiy_test_image.jpg';

        foreach (glob($directory . '/*') as $file) {
            if (is_file($file) && strpos(basename($file), $substring) !== false) {
                unlink($file);
            }
        }
    }


}
