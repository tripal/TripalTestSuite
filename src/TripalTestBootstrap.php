<?php

namespace StatonLab\TripalTestSuite;

use StatonLab\TripalTestSuite\Database\LoadsDatabaseSeeders;
use StatonLab\TripalTestSuite\Services\BootstrapDrupal;

class TripalTestBootstrap
{
    use LoadsDatabaseSeeders;

    /**
     * TripalTestBootstrap constructor.
     *
     * Bootstrap the application.
     *
     * @throws \Exception
     */
    public function __construct($load_seeders = true)
    {

        // run the Drupal bootstrap commands
        (new BootstrapDrupal())->run();

        // Get the factories
        $this->loadFactories();

        if ($load_seeders) {
            // Load Database Seeders
            $this->loadDatabaseSeeders();
        }

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
     * Print debuggable stack trace.
     */
    public static function shutdownHandler()
    {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            // fatal error
            debug_print_backtrace();
        }
    }
}
