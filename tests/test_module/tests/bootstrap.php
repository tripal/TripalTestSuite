<?php
if (! function_exists('load_php_files')) {
    function load_php_files($dir)
    {
        $dir = __DIR__.'/'.$dir;

        foreach (glob($dir.'/*.php') as $file) {
            require_once $file;
        }
    }
}

// The order here matters since we are not using autoload
load_php_files('TripalTestSuite/Database');
load_php_files('TripalTestSuite');
load_php_files('TripalTestSuite/Services');
load_php_files('TripalTestSuite/Mocks');
load_php_files('TripalTestSuite/Exceptions');

new \StatonLab\TripalTestSuite\TripalTestBootstrap();
