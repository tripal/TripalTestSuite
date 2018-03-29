<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

use StatonLab\TripalTestSuite\Exceptions\FileNotFoundException;
use Symfony\Component\Console\Input\InputArgument;

class MakeTestCommand extends BaseCommand
{
    /**
     * Configure the command.
     */
    public function configure()
    {
        $this->setName('make:test')
            ->setHelp('make:test ExampleTest')
            ->setDescription('Creates a database seeder file.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the test such as ExampleTest');
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        try {
            $this->checkTestsFolder();
        } catch (FileNotFoundException $exception) {
            $this->error($exception->getMessage());
            $message = "Please make sure you are running this command from the module's root directory. ";
            $message .= "If you have not run `tripaltest init` yet, please run it before running this command again.";
            $this->error($message);
        }

        try {
            $path = $this->makeTest();
            $this->info("Test $path was created successfully.");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Verifies the tests folder is present.
     *
     * @throws FileNotFoundException
     */
    protected function checkTestsFolder()
    {
        $path = getcwd().'/tests';
        if (! file_exists($path)) {
            throw new FileNotFoundException('Tests folder not found.');
        }
    }

    /**
     * Make the test file.
     *
     * @throws \Exception
     */
    protected function makeTest()
    {
        $name = $this->getArgument('name');
        $path = getcwd()."/tests/{$name}.php";

        if(file_exists($path)) {
            throw new \Exception("File already exists at $path");
        }

        $stub = __DIR__.'/../../../stubs/TestStub.php';
        $this->extractNameSpace($path);
        $content = file_get_contents($stub);
        $content = str_replace('$$$TEST_NAME$$', $this->extractFileName($path), $content);
        $content = str_replace('$$NAME_SPACE$$', $this->extractNameSpace($path), $content);
        $done = file_put_contents($path, $content);

        if ($done === false) {
            throw new \Exception("Could not create file at $path");
        }

        return $path;
    }

    /**
     * Extract the name of the test.
     *
     * @param $path
     * @return string
     */
    protected function extractFileName($path)
    {
        return basename($path, '.php');
    }

    /**
     * Determine the namespace value.
     *
     * @param $path
     * @return string
     */
    protected function extractNameSpace($path)
    {
        $workingDir = getcwd();
        $namespace = 'Tests';
        $current = array_reverse(explode('/', $path));
        // Discard the file name
        array_shift($current);

        $folders = [];
        while (($folder = array_shift($current)) !== 'tests') {
            $folders[] = $folder;
        }

        $folders = array_reverse($folders);

        // Create the path
        $current = 'tests/';
        foreach($folders as $folder) {
            $current .= "/{$folder}";
            if(!file_exists("$workingDir/$current")) {
                mkdir("$workingDir/$current");
            }
        }

        return $namespace."\\".implode('\\', $folders);
    }
}
