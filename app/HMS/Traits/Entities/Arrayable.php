<?php

namespace HMS\Traits\Entities;

use HMS\Helpers\Serializers\ArrayCarbonSerializer;

trait Arrayable
{
    /**
     * @return string
     */
    public function toArray()
    {
        return (new ArrayCarbonSerializer)->serialize($this);
    }
}
