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
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_bootstrapDrupal();
        
        $traits = class_uses($this);

        if (in_array('DBTransaction', $traits)) {
            $this->DBTransactionSetUp();
        }
    }

    /**
     * Tear down the environment.
     */
    protected function tearDown()
    {
        parent::tearDown();

        $traits = class_uses($this);

        if (in_array('DBTransaction', $traits)) {
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
        if(static::$bootstrapped) {
            return;
        }

        // Read environment variables
        if (! defined('DRUPAL_ROOT')) {
            define('DRUPAL_ROOT', $this->_getDrupalRoot());
        }

        if(empty(DRUPAL_ROOT)) {
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
        if (file_exists(DRUPAL_ROOT.'/tests/.env')) {
            return DRUPAL_ROOT.'/tests/.env';
        }

        if (file_exists(DRUPAL_ROOT.'/test/.env')) {
            return DRUPAL_ROOT.'/test/.env';
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
            $file = fopen($env_file_path ,'r');
            while ($line = trim(rtrim(fgets($file), "\n"))) {
                $line++;
                if (empty($line)) {
                    continue;
                }

                if (substr($line, 0, 1) === '#') {
                    // ignore comment lines
                    continue;
                }

                if (putenv($line) === false) {
                    throw new TripalTestSuiteException("Could not read environment line $line_count: $line");
                }
            }
        }
    }
}
