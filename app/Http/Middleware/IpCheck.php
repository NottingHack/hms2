<?php

namespace App\Http\Middleware;

use Closure;

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
            return redirect('home');
        }

        return $next($request);
    }
}
