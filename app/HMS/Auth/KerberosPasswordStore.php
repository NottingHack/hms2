<?php

namespace HMS\Auth;

class KerberosPasswordStore implements PasswordStore
{
    /**
     * The KADM5 connection to use.
     * @var KADM5.
     */
    private $__krbConn;

    /**
     * The relm to use.
     * @var string
     */
    private $__realm;

    /**
     * If true, we're in debug mode and shouldn't actually take any action.
     * @var bool
     */
    private $__debug;

    /**
     * Constructor.
     */
    public function __construct($app)
    {
        $config = $app['config']->get('passwordstore.kerberos', []);

        $this->__debug = $config['debug'];
        $this->__realm = $config['realm'];
        $this->__krbConn = new \KADM5($config['username'], $config['keytab'], true); // use keytab=true
    }

    /**
     * Add a new identity with the specified username and password.
     *
     * @param  string $username
     * @param  string $password
     * @return void
     */
    public function add($username, $password)
    {
        /* Just incase some smartarse appends /admin to their handle
        * in an attempt to become a krb admin... */
        if (stristr($username, '/admin') === false) {
            try {
                $princ = new \KADM5Principal(strtolower($username));
                $this->__krbConn->createPrincipal($princ, $password);
            } catch (\Exception $e) {
                if ($this->__debug) {
                    echo "$e\n";
                }

                return false;
            }

            return true;
        } else {
            if ($this->__debug) {
                echo 'Attempt to create admin user stopped.';
            }

            return false;
        }
    }

    /**
     * Remove the specified identity.
     *
     * @param  string $username
     * @return void
     */
    public function remove($username)
    {
        try {
            $princ = $this->__krbConn->getPrincipal(strtolower($username));
            $princ->delete();
        } catch (\Exception $e) {
            if ($this->__debug) {
                echo "$e\n";
            }

            return false;
        }

        return true;
    }

    /**
     * Check if a specified identity exists.
     *
     * @param  string $username
     * @return bool
     */
    public function exists($username)
    {
        try {
            $this->__krbConn->getPrincipal(strtolower($username));
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Principal does not exist') {
                return false;
            } else {
                return;
            }
        }

        return true;
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
        try {
            $princ = $this->__krbConn->getPrincipal(strtolower($username));
            $princ->changePassword($newpassword);
        } catch (\Exception $e) {
            if ($this->__debug) {
                echo "$e\n";
            }

            return false;
        }

        return true;
    }

    /**
     * Check the password for a specified identity is correct.
     *
     * @param  string $username
     * @param  string $password
     * @return bool
     */
    public function checkPassword($username, $password)
    {
        $ticket = new \KRB5CCache();
        try {
            $ticket->initPassword(strtolower($username).'@'.$this->__realm, $password);
        } catch (\Exception $e) {
            if ($this->__debug) {
                echo "$e\n";
            }

            return false;
        }

        return true;
    }
}
