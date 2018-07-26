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
                require_once $seeder;

                // Extract the class name
                /** @var \StatonLab\TripalTestSuite\Database\Seeder $className */
                $className = $this->getClassName($seeder);

                if (! class_exists($className)) {
                    $error = "Database seeder class $className not found. Make sure the filename and the class name match.";
                    throw new TripalTestBootstrapException($error);
                }

                $this->seeders[] = $className;
            }
        }

        return $this->seeders;
    }

    /**
     * Get the class name from the file name.
     *
     * @param string $file
     * @return string
     */
    protected function getClassName($file)
    {
        return trim('Tests\\DatabaseSeeders\\'.basename($file, '.php'), '\\');
    }
}
