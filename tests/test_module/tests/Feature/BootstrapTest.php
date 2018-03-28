<?php
namespace Test\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Services\BootstrapDrupal;

class BootstrapTest extends TestCase {
    /**
     * Verify that we can find drupal.
     *
     * @throws \ReflectionException
     * @test
     */
    public function shouldFindDrupalRoot() {
        $test_case = new BootstrapDrupal();
        $method = $this->getMethod('getDrupalRoot');
        $drupal_root = $method->invoke($test_case);

        $this->assertNotEmpty($drupal_root);
        $this->assertFileExists($drupal_root.'/includes/bootstrap.inc');
    }

    /**
     * Should find the .env file that's normally in test/.env.
     *
     * @throws \ReflectionException
     * @test
     */
    public function shouldFindEnvFileSuccessfully() {
        $test_case = new BootstrapDrupal();

        $method = $this->getMethod('getEnvironmentFilePath');
        $env_file_path = $method->invoke($test_case);

        // The path should not equal false and should exist.
        $this->assertNotFalse($env_file_path);
        $this->assertFileExists($env_file_path);
    }

    /**
     * Should bootstrap Drupal successfully.
     *
     * @throws \ReflectionException
     * @test
     */
    public function shouldSuccessfullyBootstrapDrupal() {
        $test_case = new BootstrapDrupal();
        $method = $this->getMethod('run');
        $method->invoke($test_case);

        // _SERVER['REMOTE_ADDR'] should exists and equals 127.0.0.1
        $this->assertTrue(isset($_SERVER['REMOTE_ADDR']));
        $this->assertEquals('127.0.0.1', $_SERVER['REMOTE_ADDR']);

        // DRUPAL_ROOT should be defined
        $this->assertTrue(defined('DRUPAL_ROOT'));

        // We should have access to any bootstrap functions
        $this->assertTrue(function_exists('drupal_render'));
    }

    /**
     * Get a private or protected methods from a given class.
     *
     * @param string $method_name Method name
     * @param string $class_name Class name. Defaults to TripalTestCase
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected function getMethod($method_name, $class_name = '') {
        if(empty($class_name)) {
            $class_name = BootstrapDrupal::class;
        }

        $reflection = new \ReflectionClass($class_name);
        $method = $reflection->getMethod($method_name);
        $method->setAccessible(true);

        return $method;
    }
}
