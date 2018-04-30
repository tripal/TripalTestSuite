<?php

namespace test_module\tests\Feature;

use PHPUnit\Framework\TestCase;
use Tests\DatabaseSeeders\UsersTableSeeder;

class DatabaseSeederTest extends TestCase
{
    /**
     * Tests whether database seeders are found and are runnable.
     *
     * @test
     */
    public function testThatSeederProvidesASeedMethod() {
        $seeder = UsersTableSeeder::seed();

        $this->assertTrue($seeder instanceof UsersTableSeeder);
    }
}
