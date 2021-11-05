<?php

namespace HMS\Entities\Tools;

abstract class ToolRestriction
{
    /**
     * Tool can be freely used by any member.
     */
    public const UNRESTRICTED = 'UNRESTRICTED';

    /**
     * Tool requires an induction before use.
     */
    public const RESTRICTED = 'RESTRICTED';
}
