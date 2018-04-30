<?php

namespace StatonLab\TripalTestSuite\Database;

use StatonLab\TripalTestSuite\Concerns\PublishesData;

abstract class Seeder
{
    use PublishesData;

    /**
     * Initialize and run the seeder.
     *
     * @return Seeder
     */
    public static function seed()
    {
        $seeder = new static();
        $seeder->up();

        return $seeder;
    }

    /**
     * Add data to the database.
     *
     * @return void
     */
    abstract public function up();
}
