[![Build Status](https://travis-ci.org/statonlab/TripalTestSuite.svg?branch=master)](https://travis-ci.org/statonlab/TripalTestSuite) [![DOI](https://zenodo.org/badge/123318173.svg)](https://zenodo.org/badge/latestdoi/123318173)

## Tripal Test Suite

**TripalTestSuite** is a composer package that handles
common test practices such as bootstrapping Drupal
before running the tests, creating test file, creating
and managing database seeders (files that seed the database
with data for use in testing) and much more.

## Installation

Within your Drupal module path (e,g sites/all/modules/my_module), run the following.

```bash 
composer require statonlab/tripal-test-suite --dev
```

This will install TripalTestSuite along with all of the dependencies.

## Usage Documentation
Please visit our [online documentation](https://tripaltestsuite.readthedocs.io/en/latest) to learn about installation and usage.

## Video Tutorials

#### Creating and Running Basic Tests with Tripal Test Suite

[![](http://img.youtube.com/vi/hxuiDzRqs9U/0.jpg)](http://www.youtube.com/watch?v=hxuiDzRqs9U "Creating and Running Basic Tests with Tripal Test Suite")


## License
TripalTestSuite is licensed under [GPLv3](LICENSE).
