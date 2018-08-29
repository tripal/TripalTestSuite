<?php

namespace test_module\tests\Feature;

use StatonLab\TripalTestSuite\Database\Factory;
use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\Mocks\CallableMock;
use StatonLab\TripalTestSuite\TripalTestCase;

class DatabaseFactoryTest extends TripalTestCase
{
    use DBTransaction;

    /**
     * Tests that factories take primary keys into account.
     *
     * @test
     */
    public function testDefiningAFactoryConsidersGivenPrimaryKey()
    {
        // Explicitly defined primary key
        Factory::define('chado.environment', new CallableMock, 'test_key_id');

        $this->assertArrayHasKey('chado.environment', Factory::$factories);
        $this->assertEquals(Factory::$factories['chado.environment']['primary_key'], 'test_key_id');

        // Grabs primary_key from chado schema
        Factory::define('chado.analysis', new CallableMock);
        $factory = factory('chado.analysis');
        $this->assertEquals($factory->primaryKey(), 'analysis_id');
    }

    /**
     * Test that factory throws an exception when primary key is not
     * explicitly defined and could not be determined.
     *
     * @test
     */
    public function testFactoryThrowsAnExceptionWhenPrimaryKeyIsNotFound()
    {
        $this->expectException(\Exception::class);
        Factory::define('watchdog', new CallableMock);
        factory('watchdog');
    }

    /**
     * Tests whether default factories succeed.
     *
     * @throws \Exception
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

    /**
     * Test factory defaults can be overridden.
     *
     * @throws \Exception
     * @test
     */
    public function testFactoryDefaultsCanBeOverridden()
    {
        $cv = factory('chado.cv')->create();

        $cvterm = factory('chado.cvterm')->create([
            'cv_id' => $cv->cv_id,
        ]);

        $this->assertEquals($cv->cv_id, $cvterm->cv_id, 'Factory defaults failed to get overridden using create()');
    }

    /**
     * Test that a defined factory exists and that non-defined one doesn't.
     *
     * @test
     */
    public function testFactoryExistsMethodReturnsBoolean() {
        Factory::define('test_table', new CallableMock);
        $exists = Factory::exists('test_table');
        $this->assertTrue($exists);

        $doesntExist = Factory::exists('not_in_a_million_years_table');
        $this->assertFalse($doesntExist);
    }
}
