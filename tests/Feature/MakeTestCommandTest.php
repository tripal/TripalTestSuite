<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Console\Commands\MakeTestCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeTestCommandTest extends TestCase
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
    protected function setUp(): void
    {
        // Since the command is supposed to create the test in the
        // current working directory, we'll create a debug folder and run
        // the command in it to test the produced files.
        $this->cwd = getcwd();
        $this->debugFolder = 'Debug-'.uniqid();
        mkdir($this->debugFolder);

        // Move into the directory to start the tests.
        chdir($this->debugFolder);
        mkdir('tests');

        // Now we can create the application and initiate the command tester
        $app = new Application();
        $app->add(new MakeTestCommand());

        $this->command = $app->find('make:test');
        $this->tester = new CommandTester($this->command);
    }

    /**
     * Return to the correct path and remove the debug directory.
     */
    protected function tearDown(): void
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
    public function testSeederThrowsExceptionIfNoArgumentsSupplied()
    {
        $this->expectException(\Exception::class);

        // Execute the command
        $this->tester->execute([
            'command' => $this->command->getName(),
        ]);
    }

    /**
     * Test that the file gets created.
     */
    public function testFileExists()
    {
        // Execute the command
        $this->tester->execute([
            'command' => $this->command->getName(),

            // arguments
            'name' => 'MyTest',
        ]);

        // Make sure we return successfully
        $this->assertTrue(strstr($this->tester->getDisplay(), 'successfully') !== false);

        // Verify the file exists
        $path = "tests/MyTest.php";
        $this->assertTrue(file_exists($path), "Failed to find $path");

        // Verify the class name and namespace are correct
        $content = file_get_contents($path);
        $exists = strpos($content, 'MyTest') !== false;
        $this->assertTrue($exists, "Failed to find the class name in the resulting file.");
        $namespace = strpos($content, 'namespace Tests') !== false;
        $this->assertTrue($namespace, "Failed to find the namespace in the resulting file.");
    }

    /**
     * Test that the file gets created in the correct sub directory.
     */
    public function testFileExistsInSubDirectory()
    {
        // Execute the command
        $name = 'Commands/Unit/MyTest';
        $this->tester->execute([
            'command' => $this->command->getName(),

            // arguments
            'name' => $name,
        ]);

        // Make sure we return successfully
        $this->assertTrue(strstr($this->tester->getDisplay(), 'successfully') !== false);

        // Verify the file exists
        $path = "tests/$name.php";
        $this->assertTrue(file_exists($path), "Failed to find $path");

        // Verify the class name and namespace are correct
        $content = file_get_contents($path);
        $exists = strpos($content, 'MyTest') !== false;
        $this->assertTrue($exists, "Failed to find the class name in the resulting file.");
        $namespace = strpos($content, 'namespace Tests\\Commands\\Unit') !== false;
        $this->assertTrue($namespace, "Failed to find the namespace in the resulting file.");
    }
}
