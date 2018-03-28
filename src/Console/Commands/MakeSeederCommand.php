<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use StatonLab\TripalTestSuite\Exceptions\FileNotFoundException;

class MakeSeederCommand extends BaseCommand
{
    /**
     * Configure the command.
     */
    public function configure()
    {
        $this->setName('make:seeder')
            ->setHelp('make:seeder ExampleTableSeeder')
            ->setDescription('Creates a database seeder file.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the seeder such as ExampleTableSeeder');
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        try {
            $this->createDatabaseSeedersFolder();
        } catch (FileNotFoundException $exception) {
            $this->error($exception->getMessage());
            $message = "Please make sure you are running this command from the module's root directory. ";
            $message .= "If you have not run `tripaltest init` yet, please run it before running this command again";
            $this->error($message);
        }

        try {
            $path = $this->makeSeeder();
            $this->line("Seeder $path was created successfully.");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Creates the database seeders folder if it doesn't exist.
     *
     * @throws FileNotFoundException
     */
    protected function createDatabaseSeedersFolder()
    {
        $path = getcwd().'/tests';
        if (! file_exists($path)) {
            throw new FileNotFoundException('Tests folder not found.');
        }

        if (! file_exists($path.'/DatabaseSeeders')) {
            mkdir($path.'/DatabaseSeeders');
        }
    }

    /**
     * Make a database seeder.
     *
     * @throws \Exception
     */
    protected function makeSeeder()
    {
        $path = getcwd().'/tests/DatabaseSeeders';
        $name = $this->getArgument('name');
        $stub = __DIR__.'/../../../stubs/SeederStub.php';
        $content = file_get_contents($stub);
        $content = str_replace('$$CLASS_NAME$$', $name, $content);
        $path = "{$path}/{$name}.php";

        if (file_exists($path)) {
            throw new \Exception("File already exists at $path");
        }

        $done = file_put_contents($path, $content);

        if ($done === false) {
            throw new \Exception("Could not create file at $path");
        }

        return $path;
    }
}
