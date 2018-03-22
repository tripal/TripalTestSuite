<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;

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
        $this->addArgument('name', InputArgument::REQUIRED, 'Specifies the module name (for example, tripal_awesome_extension).');
    }

    /**
     * Execute the command.
     */
    protected function handle()
    {
        $this->stubsDir = __DIR__.'/../../../stubs';
        $this->path = getcwd();

        $this->createTestsFolder();

        $this->copyStubs([
            'bootstrap.php' => 'tests/bootstrap.php',
            'example.env' => 'tests/example.env',
            'travis.yml' => '.travis.yml',
            'phpunit.xml' => 'phpunit.xml',
            'ExampleTest.php' => 'tests/ExampleTest.php',
        ]);

        try {
            $this->configureVariables([
                '.travis.yml' => [
                    '$$MODULE_NAME$$' => $this->getArgument('name')
                ]
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
     * Copy stubs to their paths.
     *
     * @param array $files List of files in the following structure: [STUB_FILENAME => NEW_PATH]
     */
    protected function copyStubs(array $files)
    {
        foreach ($files as $file => $to) {
            $to = "{$this->path}/{$to}";
            if (! file_exists($to)) {
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

            return;
        }

        $this->line('TRIPALTEST: tests folder already exists.');
    }

    /**
     * Finds a replaces variables in given files.
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
