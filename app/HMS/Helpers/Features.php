<?php

namespace HMS\Helpers;

class Features
{
    /**
     * Is the given feature enabled.
     *
     * @param string $feature
     *
     * @return bool
     */
    public static function isEnabled(string $feature)
    {
        $state = config('hms.features.' . $feature);

        return $state ?: false;
    }

    /**
     * Is the given feature disabled.
     *
     * @param string $feature
     *
     * @return bool
     */
    public static function isDisabled(string $feature)
    {
        $state = config('hms.features.' . $feature);

        return is_null($state) ? true : (! $state);
    }
}
