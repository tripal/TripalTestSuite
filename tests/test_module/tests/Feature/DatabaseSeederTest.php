<?php

namespace test_module\tests\Feature;

use StatonLab\TripalTestSuite\TripalTestBootstrap;
use PHPUnit\Framework\TestCase;

class DatabaseSeederTest extends TestCase
{
    /**
     * Tests whether database seeders are found and run.
     * 
     * @test
     */
    public function testDatabaseSeedersHaveRan()
    {
        // When the database seeder runs, the static loadedSeeders
        // variable get's populated with seeders that have been initialized
        $count = count(TripalTestBootstrap::$loadedSeeders);
        $this->assertTrue($count > 0);
    }
}
