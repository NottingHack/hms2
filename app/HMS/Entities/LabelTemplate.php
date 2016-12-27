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

    /**
     * Gets the primary key.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->template_name;
    }

    /**
     * Sets the primary key.
     *
     * @param string $template_name the template name
     *
     * @return self
     */
    protected function setTemplateName($template_name)
    {
        $this->template_name = $template_name;

        return $this;
    }

    /**
     * Gets the value of template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the value of template.
     *
     * @param string $template the template
     *
     * @return self
     */
    protected function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}
