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

    protected $module_name;


    public function run()
    {
        $srcdir = __DIR__;
        $root =  getcwd();
        $this->root = $root;
        $this->vendor_root = __DIR__ . '/../';

        $position = strrpos($root, '/') + 1;
        $module_name = substr($root, $position);

        $this->module_name = $module_name;
        print("\nmodule:  ". $module_name . " \n");
        $this->setUpTests();
        $this->addTravis();
        $this->append_to_gitignore();
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
            //Replace the MODULE_NAME variable with the module name
            $this->replace_string_in_file($root . "/.travis.yml", "MODULE_NAME", $this->module_name);
        }
    }

    /**
    *
    * Check if theres a gitignore in the project root.  if no, create and append vendor folder.
    * If yes, check if vendor is in there and append it if it isnt.
    */

    protected function append_to_gitignore()
    {
        $root = $this->root;
        $filepath = $root . "./.gitignore";

        if (!file_exists($filepath)) {
            $fh = fopen($filepath, 'w');
            fwrite($fh, "\nvendor/\n");
        } else {
            if (strpos(file_get_contents($root . "./.gitignore", "vendor/")) == false) {
                $fh =  fopen($myFile, 'a');
                fwrite($fh, "\nvendor/\n");
            }
        }
    }

    protected function replace_string_in_file($filename, $string_to_replace, $replace_with)
    {
        $content=file_get_contents($filename);
        $content_chunks=explode($string_to_replace, $content);
        $content=implode($replace_with, $content_chunks);
        file_put_contents($filename, $content);
    }
}
