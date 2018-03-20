<?php
load_php_files('TripalTestSuite');
load_php_files('TripalTestSuite/Exceptions');

function load_php_files($dir) {
    $dir = __DIR__.'/'.$dir;

    foreach (glob($dir.'/*.php') as $file) {
        require_once $file;
    }
}
