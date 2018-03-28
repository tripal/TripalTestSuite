<?php

namespace StatonLab\TripalTestSuite\Database;

use StatonLab\TripalTestSuite\Exceptions\TripalTestBootstrapException;

trait LoadsDatabaseSeeders
{
    /**
     * List of loaded seeders.
     *
     * @var array
     */
    protected $seeders = [];

    /**
     * Load the database seeders.
     *
     * @return mixed
     * @throws TripalTestBootstrapException
     */
    public function loadDatabaseSeeders()
    {
        $workingDir = getcwd();
        if (file_exists("$workingDir/tests/DatabaseSeeders")) {
            foreach (glob("$workingDir/tests/DatabaseSeeders/*.php") as $seeder) {
                require $seeder;

                // Extract the class name and check if it should run automatically
                /** @var \StatonLab\TripalTestSuite\Database\Seeder $className */
                $className = $this->getClassName($seeder);

                if (! class_exists($className)) {
                    $error = "Database seeder class $className not found. Make sure the filename and the class name match.";
                    throw new TripalTestBootstrapException($error);
                }

                if ($className::$auto_run) {
                    /** @var \StatonLab\TripalTestSuite\Database\Seeder $class */
                    $class = new $className();
                    $class->up();
                    $this->seeders[] = $class;
                }
            }
        }

        return $this->seeders;
    }

    /**
     * Destruct all seeders that have been run automatically.
     */
    public function databaseSeederTearDown()
    {
        foreach ($this->seeders as $seeder) {
            /** @var \StatonLab\TripalTestSuite\Database\Seeder $seeder */
            $seeder->down();
        }
    }

    /**
     * Get the class name from the file name.
     *
     * @param $file
     * @return string
     */
    protected function getClassName($file)
    {
        return 'Tests\\DatabaseSeeders\\'.basename($file, '.php');
    }
}
