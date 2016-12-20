<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;

class LabelTemplate
{
    use SoftDeletable, Timestampable;

    /**
     * primary key
     * @var string
     */
    protected $template_name;

    /**
     * @var string
     */
    protected $template;


}
