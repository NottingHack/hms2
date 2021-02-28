<?php

namespace HMS\Auth;

use KADM5;
use Exception;
use KRB5CCache;
use KADM5Principal;
use Illuminate\Support\Facades\Log;

class KerberosPasswordStore implements PasswordStore
{
    /**
     * The KADM5 connection to use.
     *
     * @var KADM5
     */
    private $krbConn;

    /**
     * The relm to use.
     *
     * @var string
     */
    private $realm;

    /**
     * If true, we're in debug mode and shouldn't actually take any action.
     *
     * @var bool
     */
    private $debug;

    /**
     * Username for use with KADM5.
     *
     * @var string
     */
    protected $username;

    /**
     * Keytab file path.
     *
     * @var string
     */
    protected $keytab;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->debug = $config['debug'];
        $this->realm = $config['realm'];
        $this->username = $config['username'];
        $this->keytab = $config['keytab'];
    }

    /**
     * Lazy initialize the KADM5 connection as it leaks sockets.
     */
    protected function initAdmin()
    {
        if (is_null($this->krbConn)) {
            $this->krbConn = new KADM5($this->username, $this->keytab, true); // use keytab=true
        }
    }

    /**
     * Add a new identity with the specified username and password.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function add($username, $password)
    {
        $this->initAdmin();

        /* Just incase some smartarse appends /admin to their handle
        * in an attempt to become a krb admin... */
        if (stristr($username, '/admin') === false) {
            try {
                $princ = new KADM5Principal(strtolower($username));
                $this->krbConn->createPrincipal($princ, $password);
            } catch (Exception $e) {
                if ($this->debug) {
                    Log::warning('KerberosPasswordStore@add: ' . $e->getMessage());
                }

                return false;
            }

            return true;
        } else {
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@add: Attempt to create admin user stopped.');
            }

            return false;
        }
    }

    /**
     * Remove the specified identity.
     *
     * @param string $username
     *
     * @return void
     */
    public function remove($username)
    {
        $this->initAdmin();

        try {
            $princ = $this->krbConn->getPrincipal(strtolower($username));
            $princ->delete();
        } catch (Exception $e) {
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@remove: ' . $e->getMessage());
            }

            return false;
        }

        return true;
    }

    /**
     * Check if a specified identity exists.
     *
     * @param string $username
     *
     * @return bool
     */
    public function exists($username)
    {
        $this->initAdmin();

        try {
            $this->krbConn->getPrincipal(strtolower($username));
        } catch (Exception $e) {
            if ($e->getMessage() == 'Principal does not exist') {
                return false;
            } else {
                return null;
            }
        }

        return true;
    }

    /**
     * Set the password for a specified identity.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function setPassword($username, $password)
    {
        $this->initAdmin();

        try {
            $princ = $this->krbConn->getPrincipal(strtolower($username));
            $princ->changePassword($password);
        } catch (Exception $e) {
            // if 'Principal does not exist' we add the missing account
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@setPassword: ' . $e->getMessage());
            }

            return $this->add($username, $password);
        }

        return true;
    }

    /**
     * Check the password for a specified identity is correct.
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($username, $password)
    {
        $ticket = new KRB5CCache();

        try {
            $ticket->initPassword(strtolower($username) . '@' . $this->realm, $password);
        } catch (Exception $e) {
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@checkPassword: ' . $e->getMessage());
            }

            return false;
        }

        return true;
    }
}
