<?php

namespace HMS\Behat\Context;

/**
 * This trait is pulled into Behat testing contexts to carry out some automated
 * operations when carrying out testing scenarios. The annotations BeforeScenario
 * and AfterScenario cause these methods to be called before and after each and
 * every testing scenario.
 *
 * In this case the methods ensure that the database is cleared and re-migrated
 * (and seeded) for every test. This means that each test can rely on the system
 * being consistant.
 *
 * As you can appreciate this means you want to be running a testing specific
 * database for your tests, which is what the .env.behat.yml file sets things up
 * to do.
 */
trait DatabaseMigrations
{
    /**
     * Migrate the database.
     *
     * @BeforeScenario
     */
    public static function migrate()
    {
        \Artisan::call('doctrine:migrations:migrate');
    }

    /**
     * Roll it back after the scenario.
     *
     * @AfterScenario
     */
    public static function refresh()
    {
        \Artisan::call('doctrine:migrations:rollback');
    }
}
