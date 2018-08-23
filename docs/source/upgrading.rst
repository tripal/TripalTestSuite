Upgrading TripalTestSuite
*************************

Since we are using composer to manage releases, running ``composer update`` should update
all your dependencies to the latest version. However, you need to be aware of
how [composer deals with versioning](https://getcomposer.org/doc/articles/versions.md).

Upgrading to a major versions (e.g, from 1.5.0 to 2.0.0), will require that you change
the specified version in your composer.json file. Upgrading minor version (e.g, 1.0.0 to 1.1.0)
can be made automatic by specifying ``1.*`` as your ``tripal-test-suite`` version.
