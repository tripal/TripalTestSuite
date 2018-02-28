<?php

namespace StatonLab\TripalTestSuite;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Exceptions\TripalTestSuiteException;

class TripalTestCase extends TestCase
{
    /**
     * @var bool
     */
    public static $bootstrapped = false;

    /**
     * Set up the environment.
     *
     * @throws \StatonLab\TripalTestSuite\Exceptions\TripalTestSuiteException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_bootstrapDrupal();

        if (property_exists($this, 'DBTransactionSetUp')) {
            $this->DBTransactionSetUp();
        }
    }

    /**
     * Tear down the environment.
     */
    protected function tearDown()
    {
        parent::tearDown();

        if (property_exists($this, 'DBTransactionTearDown')) {
            $this->DBTransactionTearDown();
        }
    }

    /**
     * Bootstrap Drupal.
     *
     * @throws \Statonlab\TripalTestSuite\Exceptions\TripalTestSuiteException
     */
    protected function _bootstrapDrupal()
    {
        // Don't bootstrap twice in runtime
        if (static::$bootstrapped) {
            return;
        }

        // Read environment variables to set drupal root
        $this->_readEnvironmentFile();

        // Read environment variables
        if (! defined('DRUPAL_ROOT')) {
            define('DRUPAL_ROOT', $this->_getDrupalRoot());
        }

        if (empty(DRUPAL_ROOT)) {
            throw new TripalTestSuiteException('DRUPAL_ROOT is not configured correctly. Please use .env files to set DRUPAL_ROOT.');
        }

        require_once DRUPAL_ROOT.'/includes/bootstrap.inc';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $cwd = getcwd();
        chdir(DRUPAL_ROOT);
        // The bootstrap function won't run twice since drupal checks if it has run it before
        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
        chdir($cwd);

        static::$bootstrapped = true;
    }

    /**
     * Gets Drupal root path.
     *
     * @return string
     */
    protected function _getDrupalRoot()
    {
        if($path = getenv('DRUPAL_ROOT')) {
            return $path;
        }

        $path = getcwd();
        while ($path !== '/') {
            if (file_exists($path.'/includes/bootstrap.inc')) {
                return $path;
            }

            $path = dirname($path);
        }

        return '';
    }

    /**
     * Look for environment variable file.
     *
     * @return bool|string
     */
    protected function _getEnvironmentFilePath()
    {
        $dir = getcwd();

        // If running phpunit from the module root dir
        if (file_exists($dir.'/tests/.env')) {
            return $dir.'/tests/.env';
        }

        // If running phpunit from the module root dir
        if (file_exists($dir.'/test/.env')) {
            return $dir.'/test/.env';
        }

        // If running php unit from within the tests dir
        if (file_exists($dir.'/.env')) {
            return $dir.'/.env';
        }

        return false;
    }

    /**
     * Read env files.
     *
     * @throws \Statonlab\TripalTestSuite\Exceptions\TripalTestSuiteException
     */
    protected function _readEnvironmentFile()
    {
        $env_file_path = $this->_getEnvironmentFilePath();

        if ($env_file_path) {
            $line_count = 0;
            $file = fopen($env_file_path, 'r');
            while ($line = fgets($file)) {
                $line++;
                if (empty($line)) {
                    continue;
                }

                if (substr($line, 0, 1) === '#') {
                    // Ignore comment lines
                    continue;
                }

                if (putenv(str_replace("\n", '', trim($line))) === false) {
                    throw new TripalTestSuiteException("Could not read environment line $line_count: $line");
                }
            }
        }
    }
}
