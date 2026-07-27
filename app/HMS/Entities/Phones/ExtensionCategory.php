<?php

namespace HMS\Entities\Phones;

abstract class ExtensionCategory
{
    /*
     * Member
     */
    public const MEMBER = 'MEMBER';

    /*
     * Area
     */
    public const AREA = 'AREA';

    /*
     * Service
     */
    public const SERVICE = 'SERVICE';

    /**
     * String representations of each type.
     */
    public const TYPE_STRINGS = [
        self::MEMBER => 'Member / Individual',
        self::AREA => 'Location',
        self::SERVICE => 'Service',
    ];
}
