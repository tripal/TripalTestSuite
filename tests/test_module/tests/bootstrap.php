<?php
/**
 * Auto load required files.
 *
 * @author https://www.php-fig.org/psr/psr-4/examples/
 */
spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'StatonLab\\TripalTestSuite\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__.'/../TripalTestSuite/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir.str_replace('\\', '/', $relative_class).'.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__.'/../TripalTestSuite/Helpers/helpers.php';

new \StatonLab\TripalTestSuite\TripalTestBootstrap();
