<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use PHPUnit\Framework\TestCase;

class HelloWorldTest extends TestCase
{
    protected $webDriver;

    public function setUp(): void
    {
        $host = 'http://localhost:4444'; // URL of the Selenium server
        $capabilities = Facebook\WebDriver\Remote\DesiredCapabilities::chrome();
        $this->webDriver = RemoteWebDriver::create($host, $capabilities);
    }

    public function tearDown(): void
    {
        $this->webDriver->quit();
    }

    public function testSearch()
    {
        $this->webDriver->get("http://www.google.com");

        // find search box
        $searchBox = $this->webDriver->findElement(WebDriverBy::name("q"));
        $searchBox->sendKeys("Hello World");

        // submit the form
        $searchBox->submit();

        // wait for the results to show up
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains("Hello World")
        );

        // Assert the title contains "Hello World"
        $this->assertStringContainsString("Hello World", $this->webDriver->getTitle());
    }
}
