<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

use StatonLab\TripalTestSuite\Database\LoadsDatabaseSeeders;
use StatonLab\TripalTestSuite\Database\Seeder;
use Symfony\Component\Console\Input\InputArgument;

class DBSeedCommand extends BaseCommand
{
    use LoadsDatabaseSeeders;

    /**
     * Configure the command.
     */
    public function configure()
    {
        $this->setName('db:seed')
            ->setHelp('db:seed [ExampleTableSeeder]')
            ->setDescription('Runs all seeders or a specific seeder class if provided');

        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of the seeder to run such as ExampleTableSeeder');
    }

    /**
     * Seed the database.
     * @throws \Exception
     */
    public function handle()
    {
        $this->loadDatabaseSeeders();

        $seeder = $this->getArgument('name');

        if ($seeder) {
            $this->runSeeder($seeder);
            $this->success($seeder);
            return;
        }

        if(empty($this->seeders)) {
            $this->error('No database seeders found!');
            return;
        }

        foreach ($this->seeders as $seeder) {
            $this->runSeeder($seeder);
            $this->success($seeder);
        }
    }

    /**
     * Prints success message.
     *
     * @param $seeder
     */
    protected function success($seeder) {
        $this->info("Ran $seeder successfully!");
    }

    /**
     * Get the fully qualified seeder name.
     *
     * @param $name
     * @return string
     */
    protected function prepSeeder($name)
    {
        $name = trim($name, '\\');
        if (strstr($name, 'Tests\\DatabaseSeeders') !== false) {
            return $name;
        }

        return 'Tests\\DatabaseSeeders\\'.$name;
    }

    /**
     * Run a given seeder.
     *
     * @param string $class
     * @return \StatonLab\TripalTestSuite\Database\Seeder
     * @throws \Exception
     */
    protected function runSeeder($class)
    {
        /** @var Seeder $class */
        $class = $this->prepSeeder($class);

        if(!in_array($class, $this->seeders)) {
            throw new \Exception("Seeder $class Not Found!");
        }

        return $class::seed();
    }
}
