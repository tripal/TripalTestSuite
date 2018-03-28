<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Whether to run the seeder automatically before
     * starting our tests.
     *
     * @var bool
     */
    public static $auto_run = true;

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
        $new_user = [
            'name' => 'test user',
            'pass' => 'secret',
            'mail' => 'test@example.com',
            'status' => 1,
            'init' => 'Email',
            'roles' => [
                DRUPAL_AUTHENTICATED_RID => 'authenticated user',
            ],
        ];

        // The first parameter is sent blank so a new user is created.
        $this->users[] = user_save(new \stdClass(), $new_user);
    }

    /**
     * Cleans up the database from the created users.
     */
    public function down()
    {
        foreach ($this->users as $user) {
            user_delete($user->uid);
        }
    }
}
