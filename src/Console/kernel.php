<?php
/**
 * Add list of commands here to auto bootstrap on launch.
 */
return [
    \StatonLab\TripalTestSuite\Console\Commands\InitCommand::class,
    \StatonLab\TripalTestSuite\Console\Commands\WelcomeCommand::class,
    \StatonLab\TripalTestSuite\Console\Commands\MakeTestCommand::class,
    \StatonLab\TripalTestSuite\Console\Commands\MakeSeederCommand::class,
    \StatonLab\TripalTestSuite\Console\Commands\DBSeedCommand::class,
];
