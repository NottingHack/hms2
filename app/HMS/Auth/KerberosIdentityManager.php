<?php

namespace HMS\Auth;

class KerberosIdentityManager implements IdentityManager
{
    /**
     * Add a new identity with the specified username and password.
     *
     * @param  string $username
     * @param  string $password
     * @return void
     */
    public function add($username, $password)
    {
        // TODO: Implement add() method.
    }

    /**
     * Remove the specified identity.
     *
     * @param  string $username
     * @return void
     */
    public function remove($username)
    {
        // TODO: Implement remove() method.
    }

    /**
     * Check if a specified identity exists.
     *
     * @param  string $username
     * @return boolean
     */
    public function exists($username)
    {
        // TODO: Implement exists() method.
    }

    /**
     * Set the password for a specified identity.
     *
     * @param  string $username
     * @param  string $password
     * @return void
     */
    public function setPassword($username, $password)
    {
        // TODO: Implement setPassword() method.
    }

    /**
     * Check the password for a specified identity is correct.
     *
     * @param  string $username
     * @param  string $password
     * @return boolean
     */
    public function checkPassword($username, $password)
    {
        // TODO: Implement checkPassword() method.
    }
}