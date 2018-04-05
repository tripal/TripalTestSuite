<?php

namespace StatonLab\TripalTestSuite;

use StatonLab\TripalTestSuite\Database\LoadsDatabaseSeeders;
use StatonLab\TripalTestSuite\Services\BootstrapDrupal;

class TripalTestBootstrap
{
    use LoadsDatabaseSeeders;

    /**
     * Save all seeders that have been run automatically
     * to run their down method in case of a fatal error.
     *
     * @var array
     */
    public static $loadedSeeders = [];

    /**
     * TripalTestBootstrap constructor.
     *
     * Bootstrap the application.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        // run the Drupal bootstrap commands
        (new BootstrapDrupal())->run();

        // Get the factories
        $this->loadFactories();

        // Run any seeders set to run automatically
        static::$loadedSeeders = $this->loadDatabaseSeeders();

        // Add shutdown handler to revert database seeders
        $this->registerErrorHandler();
    }

    /**
     * Load data factories.
     */
    public function loadFactories()
    {
        $path = getcwd().'/tests/DataFactory.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }

    /**
     * Register error handlers.
     */
    public function registerErrorHandler()
    {
        register_shutdown_function('\\StatonLab\\TripalTestSuite\\TripalTestBootstrap::shutdownHandler');
    }

    /**
     * Revert seeders in case of a fatal error.
     */
    public static function shutdownHandler()
    {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            // fatal error
            foreach (static::$loadedSeeders as $seeder) {
                $seeder->down();
            }
        }
    }

    /**
     * Clean up.
     */
    public function __destruct()
    {
        // Run the down method for all seeders that got run automatically
        $this->databaseSeederTearDown();
    }
}
