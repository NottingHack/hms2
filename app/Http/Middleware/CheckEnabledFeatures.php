<?php

namespace App\Http\Middleware;

use Closure;
use HMS\Helpers\Features;

class CheckEnabledFeatures
{
    /**
     * The features instance.
     *
     * @var \HMS\Helpers\Features
     */
    protected $features;

    /**
     * Create a new middleware instance.
     *
     * @param \HMS\Helpers\Features  $features
     *
     * @return void
     */
    public function __construct(Features $features)
    {
        $this->features = $features;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure  $next
     * @param string  ...$features
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$features)
    {
        $features = empty($features) ? [null] : $features;

        foreach ($features as $feature) {
            if ($this->features->isDisabled($feature)) {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
