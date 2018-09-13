<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InitCommand extends BaseCommand
{
    /**
     * Path to module.
     *
     * @var string
     */
    protected $path;

    /**
     * Stubs directory.
     *
     * @var string
     */
    protected $stubsDir;

    /**
     * Set command configuration.
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Setup testing environment along with Travis CI')
            ->setHelp('This command creates the tests folder, .travis.yml and phpunit.xml');

        // Add arguments
        $this->addArgument('name', InputArgument::OPTIONAL,
            'Specifies the module name (for example, tripal_awesome_extension).', $this->getModuleName());

        $this->addOption('force', 'f', InputOption::VALUE_NONE,
            'Force replacement of existing files such as phpunit.xml, .travis.yml, DataFactory.php, example.env, ExampleTest.php and bootstrap.php');
    }

    /**
     * Execute the command.
     */
    protected function handle()
    {
        $this->stubsDir = __DIR__.'/../../../stubs';
        $this->path = getcwd();

        if ($this->getOption('force') !== false) {
            $value = $this->ask("<info>Are you sure you want to force replacing files (y/N)?</info> ");

            if (! $value) {
                $this->info('Aborting');

                return;
            }
        }

        $this->createTestsFolder();

        $this->copyStubs([
            'bootstrap.php' => 'tests/bootstrap.php',
            'example.env' => 'tests/example.env',
            'travis.yml' => '.travis.yml',
            'phpunit.xml' => 'phpunit.xml',
            'ExampleTest.php' => 'tests/ExampleTest.php',
            'UsersTableSeeder.php' => 'tests/DatabaseSeeders/examples/UsersTableSeeder.php',
            'DataFactory.php' => 'tests/DataFactory.php',
            'DevSeedSeeder.php' => 'tests/DatabaseSeeders/examples/DevSeedSeeder.php',
        ]);

        try {
            $this->configureVariables([
                '.travis.yml' => [
                    '$$MODULE_NAME$$' => $this->getArgument('name'),
                ],
            ]);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

        // Done! Print success message and .gitignore instructions
        $this->info('Set up completed successfully. Please add the following lines to your .gitignore file.');
        $this->info('.env');
        $this->info('vendor/');
    }

    /**
     * Get a default module name.
     *
     * @return string
     */
    function getModuleName()
    {
        return basename(getcwd());
    }

    /**
     * Copy stubs to their paths.
     *
     * @param array $files List of files in the following structure: [STUB_FILENAME => NEW_PATH]
     */
    protected function copyStubs(array $files)
    {
        foreach ($files as $file => $to) {
            $to = "{$this->path}/{$to}";
            if (! file_exists($to) || $this->getOption('force') !== false) {
                copy("{$this->stubsDir}/{$file}", $to);
            } else {
                $this->line("TRIPALTEST: $to already exists.");
            }
        }
    }

    /**
     * Create tests folder.
     */
    protected function createTestsFolder()
    {
        if (! file_exists($this->path.'/tests')) {
            mkdir($this->path.'/tests');
        } else {
            $this->line('TRIPALTEST: tests folder already exists.');
        }

        if (! file_exists($this->path.'/tests/DatabaseSeeders')) {
            mkdir($this->path.'/tests/DatabaseSeeders');
        } else {
            $this->line('TRIPALTEST: tests/DatabaseSeeders folder already exists.');
        }

  if (! file_exists($this->path.'/tests/DatabaseSeeders/examples')) {
            mkdir($this->path.'/tests/DatabaseSeeders/examples');
        } else {
            $this->line('TRIPALTEST: tests/DatabaseSeeders/examples folder already exists.');
        }

    }

    /**
     * Finds a replaces variables in given files.
     *
     * @param array $files List of files and values in the following structure:
     *                        ['FILE_NAME' => [
     *                             "VARIABLE" => "NEW_VALUE"
     *                           ]
     *                        ]
     * @throws \Exception
     */
    protected function configureVariables(array $files)
    {
        foreach ($files as $file => $values) {
            $file = "{$this->path}/{$file}";
            if (! file_exists($file)) {
                throw new \Exception("TRIPALTEST: Unable to configure variables. $file does not exist!");
            }

            $content = file_get_contents($file);
            foreach ($values as $search => $replace) {
                $content = str_replace($search, $replace, $content);
            }

            file_put_contents($file, $content);
        }
    }
}
