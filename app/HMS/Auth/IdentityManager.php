<?php

namespace HMS\Auth;

/**
 * An IdentityManager specifies operations for adding, removing and checking the credentials of an identity.
 *
 * @author Rob Hunt <rob.hunt@nottinghack.org.uk>
 */
interface IdentityManager
{
    /**
     * Add a new identity with the specified username and password.
     *
     * @param  string  $username
     * @param  string  $password
     * @return void
     */
    public function add($username, $password);

    /**
     * Remove the specified identity.
     *
     * @param  string  $username
     * @return void
     */
    public function remove($username);

    /**
     * Check if a specified identity exists.
     *
     * @param  string  $username
     * @return boolean
     */
    public function exists($username);

    /**
     * Set the password for a specified identity.
     *
     * @param  string  $username
     * @param  string  $password
     * @return void
     */
    public function setPassword($username, $password);

    /**
     * Check the password for a specified identity is correct.
     *
     * @param  string  $username
     * @param  string  $password
     * @return boolean
     */
    public function checkPassword($username, $password);
}