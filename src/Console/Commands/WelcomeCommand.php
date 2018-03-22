<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

class WelcomeCommand extends BaseCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('welcome')
            ->setDescription('Prints welcome message.')
            ->setHelp('Provides the user with instructions to create the tests structure.');
    }

    /**
     * Run the command.
     */
    protected function handle()
    {
        $this->line('Thanks for installing Tripal Test Suite!');
        $this->info('Run "./vendor/bin/tripaltest init" to set up the tests directory, travis and phpunit along with an example test.');
    }
}
