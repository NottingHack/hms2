<?php

namespace HMS\Auth;

use Exception;
use Illuminate\Support\Facades\Log;
use KADM5;
use KADM5Principal;
use KRB5CCache;

class KerberosPasswordStore implements PasswordStore
{
    /**
     * The KADM5 connection to use.
     *
     * @var KADM5
     */
    // @phpstan-ignore class.notFound
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
        // @phpstan-ignore class.notFound
        $this->krbConn = new KADM5($this->username, $this->keytab, true); // use keytab=true
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
                // @phpstan-ignore class.notFound
                $princ = new KADM5Principal(strtolower($username));
                // @phpstan-ignore class.notFound
                $this->krbConn->createPrincipal($princ, $password);
            } catch (Exception $e) {
                if ($this->debug) {
                    Log::warning('KerberosPasswordStore@add: ' . $e->getMessage());
                }

                // TODO: re throw?
                return;
            }

            return;
        } else {
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@add: Attempt to create admin user stopped.');
            }

            // TODO: throw
            return;
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
            // @phpstan-ignore class.notFound
            $princ = $this->krbConn->getPrincipal(strtolower($username));
            $princ->delete();
        } catch (Exception $e) {
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@remove: ' . $e->getMessage());
            }

            // TODO:: throw?
            return;
        }
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
            // @phpstan-ignore class.notFound
            $this->krbConn->getPrincipal(strtolower($username));
        } catch (Exception $e) {
            if ($e->getMessage() == 'Principal does not exist') {
                return false;
            } else {
                // TODO: throw?
                return false; // null;
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
            // @phpstan-ignore class.notFound
            $princ = $this->krbConn->getPrincipal(strtolower($username));
            $princ->changePassword($password);
        } catch (Exception $e) {
            // if 'Principal does not exist' we add the missing account
            if ($this->debug) {
                Log::warning('KerberosPasswordStore@setPassword: ' . $e->getMessage());
            }

            $this->add($username, $password);
        }
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
        // @phpstan-ignore class.notFound
        $ticket = new KRB5CCache();

        try {
            // @phpstan-ignore class.notFound
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
