<?php

namespace StatonLab\TripalTestSuite\Database;

use StatonLab\TripalTestSuite\TripalTestBootstrap;

abstract class Seeder
{
    /**
     * Whether to auto run the seeder before tests begin.
     *
     * @var bool
     */
    public static $auto_run = false;

    /**
     * Initialize and run the seeder.
     *
     * @return Seeder
     */
    public static function seed()
    {
        $seeder = new static();
        $seeder->up();

        // Add the seeder the loaded seeders property to get destructed
        // in case of a fatal error.
        array_push(TripalTestBootstrap::$loadedSeeders, $seeder);

        return $seeder;
    }

    /**
     * Add data to the database.
     *
     * @return void
     */
    abstract public function up();

    /**
     * Clean up by removing the inserted data.
     *
     * @return void
     */
    abstract public function down();
}
