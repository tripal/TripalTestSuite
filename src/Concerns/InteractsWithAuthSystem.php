<?php

namespace StatonLab\TripalTestSuite\Concerns;

trait InteractsWithAuthSystem
{
    /**
     * Set the active user.
     *
     * @param int|object $user Either a user object or simply a user id.
     * @return $this
     */
    public function actingAs($uid)
    {
        global $user;

        // Load the new requested user
        if (is_object($uid)) {
            if($user->uid === 0) {
                $user = drupal_anonymous_user();
            } else{
                $user = user_load($uid->uid);
            }
        } else {
            if($uid === 0) {
                $user = drupal_anonymous_user();
            } else {
                $user = user_load($uid);
            }
        }

        $this->_cookies = [
            session_name() => session_id()
        ];

        return $this;
    }

    /**
     * Restore session data.
     */
    public function authSystemTearDown()
    {
        global $user;
        _drupal_session_destroy(session_id());
        $user = drupal_anonymous_user();
    }
}
