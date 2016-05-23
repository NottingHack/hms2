<?php

namespace HMS\Behat\Context;

trait DatabaseMigrations
{

    /**
     * Migrate the database
     *
     * @BeforeScenario
     */
    public static function migrate()
    {
        \Artisan::call('migrate');
    }

    /**
     *
     * Roll it back after the scenario.
     *
     * @AfterScenario
     */
    public static function refresh()
    {
        \Artisan::call('migrate:rollback');
    }

}
