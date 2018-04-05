<?php

namespace test_module\tests\Feature;

use PHPUnit\Framework\TestCase;

class DatabaseFactoryTest extends TestCase
{
    /**
     * Tests whether database seeders are found and run.
     *
     * @test
     */
    public function testDefaultFactoriesSucceed()
    {
        $cvs = factory('chado.cv', 10)->create();
        $this->assertEquals(count($cvs), 10, 'Failed asserting that 10 cvs have been created.');
        $this->assertObjectHasAttribute('cv_id', $cvs[0]);

        $cvterms = factory('chado.cvterm', 10)->create();
        $this->assertEquals(count($cvterms), 10, 'Failed asserting that 10 cv terms have been created.');
        $this->assertObjectHasAttribute('cvterm_id', $cvterms[0]);

        $organisms = factory('chado.organism', 10)->create();
        $this->assertEquals(count($organisms), 10, 'Failed asserting that 10 organisms have been created.');
        $this->assertObjectHasAttribute('organism_id', $organisms[0]);

        $features = factory('chado.feature', 10)->create();
        $this->assertEquals(count($features), 10, 'Failed asserting that 10 features have been created.');
        $this->assertObjectHasAttribute('feature_id', $features[0]);
    }
}
