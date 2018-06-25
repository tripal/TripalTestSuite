<?php
namespace Test\Feature;

use StatonLab\TripalTestSuite\TripalTestCase;

class AuthenticationSystemTest extends TripalTestCase
{
    public function testTheAuthenticatedUserIsCorrect() {
        global $user;

        // Anonymous user has uid = 0
        $this->assertEquals(0, $user->uid);

        // Authenticate the correct user
        $this->actingAs(1);

        $this->assertEquals(1, $user->uid);
        $this->assertContains('administrator', $user->roles);
    }

    /** @depends testTheAuthenticatedUserIsCorrect */
    public function testThatUserHasBeenUnauthenticated() {
        // Since this function depends on the one before it, the user should
        // logged out before we get to this function
        global $user;
        $this->assertEquals(0, $user->uid);
    }
}
