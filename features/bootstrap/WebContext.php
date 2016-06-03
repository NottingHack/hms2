<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\MinkContext;
use Laracasts\Behat\Context\DatabaseTransactions;
use HMS\Behat\Context\DatabaseMigrations;

/**
 * Defines application features from the specific context.
 */
class WebContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use DatabaseMigrations, DatabaseTransactions,
        ExampleTestTrait;
}
