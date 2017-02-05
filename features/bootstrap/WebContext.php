<?php

use Behat\Behat\Context\Context;
use HMS\Behat\Context\DatabaseMigrations;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Laracasts\Behat\Context\DatabaseTransactions;

/**
 * Defines application features from the specific context.
 */
class WebContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use DatabaseMigrations, DatabaseTransactions,
        ExampleTestTrait;
}
