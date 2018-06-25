<?php

namespace Tests\DatabaseSeeders;

use Faker\Factory;
use StatonLab\TripalTestSuite\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * The users that got created.
     * We save this here to have them easily deleted
     * in the down() method.
     *
     * @var array
     */
    protected $users = [];

    /**
     * Seeds the database with users.
     */
    public function up()
    {
        $faker = Factory::create();

        $new_user = [
            'name' => $faker->name,
            'pass' => 'secret',
            'mail' => $faker->email,
            'status' => 1,
            'init' => 'Email',
            'roles' => [
                DRUPAL_AUTHENTICATED_RID => 'authenticated user',
            ],
        ];

        // The first parameter is sent blank so a new user is created.
        $this->users[] = user_save(new \stdClass(), $new_user);
    }
}
