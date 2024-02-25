<?php

namespace HMS\Entities;

interface EntityObfuscatableInterface
{

    /**
     * Obfuscate an entity upon account deletion
     * or scheduled governance cleanup.
     */
    public function obfuscate();
}
