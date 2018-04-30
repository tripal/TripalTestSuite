<?php

namespace StatonLab\TripalTestSuite\Concerns;

trait InteractsWithAuthSystem
{
    /**
     * Original signed in user if any.
     *
     * @var object
     */
    protected $_original_user;

    /**
     * Old session state.
     *
     * @var bool
     */
    protected $_old_state;

    /**
     * Authenticated user account.
     *
     * @var object|null
     */
    protected $_authenticated_account = null;

    /**
     * Set the active user.
     *
     * @param int|object $user
     */
    public function actingAs($uid)
    {
        global $user;

        // Save current state
        $this->_original_user = $user;
        $this->_old_state = drupal_save_session();

        // Destroy session
        drupal_save_session(false);

        // Load the new requested user
        if (is_object($uid)) {
            $user = user_load($uid->uid);
        } else {
            $user = user_load($uid);
        }

        return $this;
    }

    /**
     * Restore session data.
     */
    public function authSystemTearDown()
    {
        global $user;

        if ($this->_authenticated_account === null) {
            return;
        }

        $user = $this->_original_user;
        drupal_save_session($this->_old_state);
        $this->_authenticated_account = null;
    }
}
