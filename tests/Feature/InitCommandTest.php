<?php
/**
 * Created by PhpStorm.
 * User: Almsaeed
 * Date: 3/22/18
 * Time: 11:29 AM
 */

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Console\Commands\InitCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends TestCase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $tester;

    /**
     * Create the command tester.
     */
    protected function setUp()
    {
        $app = new Application();
        $app->add(new InitCommand());

        $this->command = $app->find('init');
        $this->tester = new CommandTester($this->command);
    }

    /**
     * Make sure an exception is thrown when the name argument is missing.
     */
    public function testRequiredArgument()
    {
        // Expect an exception when the command argument is not provided
        $this->expectException(RuntimeException::class);
        $this->tester->execute([
            'command' => $this->command->getName(),
        ]);
    }
}
