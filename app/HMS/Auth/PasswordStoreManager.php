<?php

namespace HMS\Auth;

use Illuminate\Support\Manager;

class PasswordStoreManager extends Manager
{
    /**
     * Create an instance of the Kerberos driver.
     *
     * @return KerberosPasswordStore
     */
    protected function createKerberosDriver()
    {
        return new KerberosPasswordStore($this->config->get('passwordstore.kerberos', []));
    }

    /**
     * Create an instance of the Doctrine driver.
     *
     * @return DoctrinePasswordStore
     */
    protected function createDoctrineDriver()
    {
        return $this->container->make(DoctrinePasswordStore::class);
    }

    /**
     * Create an instance of the FileBased driver.
     *
     * @return FileBasedPasswordStore
     */
    protected function createFileBasedDriver()
    {
        return new FileBasedPasswordStore($this->config->get('passwordstore.fileBased', []));
    }

    /**
     * Get the default driver.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->container['config']['passwordstore.driver'];
    }
}
