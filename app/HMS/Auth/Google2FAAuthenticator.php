<?php

namespace HMS\Auth;

use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAAuthenticator extends Authenticator
{
    /**
     * Check if it is already logged in or passable without checking for an OTP.
     *
     * @return bool
     */
    protected function canPassWithoutCheckingOTP()
    {
        return ! $this->getUser()->isGoogle2faEnable() ||
            ! $this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            $this->twoFactorAuthStillValid();
    }

    /**
     * Get the user Google2FA secret.
     *
     * @throws InvalidSecretKey
     *
     * @return mixed
     */
    protected function getGoogle2FASecretKey()
    {
        $secret = $this->getUser()->getGoogle2faSecret();

        if (is_null($secret) || empty($secret)) {
            throw new InvalidSecretKey('Secret key cannot be empty.');
        }

        return $secret;
    }
}
