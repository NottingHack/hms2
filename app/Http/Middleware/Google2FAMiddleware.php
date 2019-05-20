<?php

namespace App\Http\Middleware;

use Closure;
use HMS\Auth\Google2FAAuthenticator;

class Google2FAMiddleware
{
    /**
     * @var Google2FAAuthenticator
     */
    protected $authenticator;

    /**
     * Construct Middleware.
     *
     * @param Google2FAAuthenticator $authenticator
     */
    public function __construct(Google2FAAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authenticator->boot($request);

        if ($this->authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $this->authenticator->makeRequestOneTimePasswordResponse();
    }
}
