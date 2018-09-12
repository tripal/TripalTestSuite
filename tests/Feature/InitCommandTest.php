<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Console\Commands\InitCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends TestCase
{
    /**
     * Holds the command.
     *
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * Holds the command tester.
     *
     * @var CommandTester
     */
    protected $tester;

    /**
     * Current working directory.
     *
     * @var string
     */
    private $cwd;

    /**
     * Path to debug folder.
     *
     * @var string
     */
    private $debugFolder;

    /**
     * Create the command tester and a debug directory.
     */
    protected function setUp()
    {
        // Since the command is supposed to create the tests scaffold in the
        // current working directory, we'll create a debug folder and run
        // the command in it to test the produced files.
        $this->cwd = getcwd();
        $this->debugFolder = 'Debug-'.uniqid();
        mkdir($this->debugFolder);

        // Move into the directory to start the tests.
        chdir($this->debugFolder);

        // Now we can create the application and initiate the command tester
        $app = new Application();
        $app->add(new InitCommand());

        $this->command = $app->find('init');
        $this->tester = new CommandTester($this->command);
    }

    /**
     * Return to the correct path and remove the debug directory.
     */
    protected function tearDown()
    {
        // Go back to where we were
        chdir($this->cwd);

        // Clean up (Not the best way but PHP's `rmdir()` can't delete folders that are not empty. However,
        // this is safe to use since it's escaped)
        system(sprintf('rm -rf %s', escapeshellarg($this->debugFolder)));
    }

    /**
     * Make sure an exception is thrown when the name argument is missing.
     */
    public function testResultingScaffoldingIsCorrectAndComplete()
    {
        // Execute the command
        $this->tester->execute([
            'command' => $this->command->getName(),
        ]);

        // Verify all files exist
        $files = [
            "tests",
            "tests/bootstrap.php",
            "tests/ExampleTest.php",
            "tests/example.env",
            "tests/DatabaseSeeders/examples/UsersTableSeeder.php",
            "phpunit.xml",
            ".travis.yml",
        ];

        foreach ($files as $file) {
            $this->assertTrue(file_exists($file), "Failed to find {$file}");
        }

        // Make sure travis contains the correct module name, which in this case is the folder
        // name since we did not provide the module name as an argument
        $content = file_get_contents('.travis.yml');
        $moduleName = 'modules/'.basename($this->debugFolder);
        $this->assertTrue(strpos($content, $moduleName) !== false, "Failed to find the module name in .travis.yml");
    }

    /**
     * Tests that the name argument is present in .travis.yml
     * when provided.
     */
    public function testNameArgumentIsHandledCorrectly()
    {
        // Execute the command and pass it the name argument
        $name = 'my_test_module';
        $this->tester->execute([
            'command' => $this->command->getName(),

            // Name argument
            'name' => $name,
        ]);

        $content = file_get_contents('.travis.yml');
        $this->assertTrue(strpos($content, "modules/{$name}") !== false, "Failed to find $name in .travis.yml");
    }
}
