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

load_php_files('Console');
