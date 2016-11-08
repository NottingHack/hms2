<?php

use Behat\Behat\Tester\Exception\PendingException;

trait ExampleTestTrait
{
    /**
     * @Given /^I want demonstrate what a test statement definition looks like/
     */
    public function iWantToDemonstrateWhatATestStatementDefinitionLooksLike()
    {
        throw new PendingException;
    }
}
