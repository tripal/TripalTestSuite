<?php

namespace StatonLab\TripalTestSuite\Console;

use Symfony\Component\Console\Application;

class TripalTest
{
    /**
     * Symfony application.
     *
     * @var Application
     */
    protected $application;

    /**
     * TripalTest constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createApplication();
        $this->registerCommands();

        // Run the app
        $this->application->run();
    }

    /**
     * Create a symfony console application.
     */
    protected function createApplication()
    {
        $this->application = new Application('TripalTest', '0.3.0');
    }

    /**
     * Loads and registers commands.
     */
    protected function registerCommands()
    {
        // Get the kernel and extract commands
        $commands = include __DIR__.'/kernel.php';
        foreach ($commands as $command) {
            $this->application->add(new $command());
        }
    }
}
