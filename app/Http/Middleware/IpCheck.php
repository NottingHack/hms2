<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Middleware to check that the incoming request has a source IP form the hacksapce network,
 * bounce the request back to the index page if not.
 */
class IpCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! \HmsHelper::inTheSpace($request)) {
            return redirect('/');
        }

        return $next($request);
    }
}
