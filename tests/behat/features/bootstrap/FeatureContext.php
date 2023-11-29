<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ExpectationException;

class FeatureContext extends MinkContext implements Context
{
    /**
     * Initializes context.
     *
     * @param array $parameters Context parameters (set them in behat.yml)
     */
    public function __construct(array $parameters = []) {
    }
}
