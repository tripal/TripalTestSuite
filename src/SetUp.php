<?php


namespace StatonLab\TripalTestSuite;

use Exception;

class SetUp
{

  /**
   * The root directory.
   *
   * @var string
   */
    protected $root;

    protected $vendor_root;


    public function run()
    {
        $srcdir = __DIR__;
        $root =  getcwd();
        $this->root = $root;
        $this->vendor_root = __DIR__ . '/../';
        $this->setUpTests();
        $this->addTravis();
    }


    protected function setUpTests()
    {
        $root = $this->root;
        $vendor_root = $this->vendor_root;

        $this->_create_dir("tests");
        copy($vendor_root . "stubs/TripalExampleTest.php", $root . "/tests/TripalExampleTest.php");
        copy($vendor_root . "stubs/example.env", $root . "/tests/example.env");
        copy($vendor_root . "stubs/bootstrap.php", $root . "/tests/bootstrap.php");

        if (file_exists($root . "/phpunit.xml")) {
            print  "\nphpunit.xml already exists: skipping...\n";
        } else {
            copy($vendor_root . "stubs/phpunit.xml", $root . "/phpunit.xml");
        }
    }

    protected function _create_dir($dir)
    {
        $root = $this->root;
        $dir = "/" . $dir;
        if (!file_exists($root . $dir)) {
            if (!mkdir($root . $dir)) {
                throw new Exception(
        "Could not create" . $root . $dir . " !\n"
);
            };
        }
    }

    protected function addTravis()
    {
        $root = $this->root;
        $vendor_root = $this->vendor_root;

        if (file_exists($root . "/.travis.yml")) {
            print  "\n.travis.yml file already exists: skipping Travis Integration step...\n";
        } else {
            copy($vendor_root . "stubs/travis.yml", $root . "/.travis.yml");
        }
    }
}
