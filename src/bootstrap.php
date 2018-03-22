<?php
load_php_files('Console');

function load_php_files($dir) {
    $dir = __DIR__.'/'.$dir;

    foreach (glob($dir.'/*.php') as $file) {
        require_once $file;
    }
}
