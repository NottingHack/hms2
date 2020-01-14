<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;

class LabelTemplate
{
    use SoftDeletable, Timestampable;

    /**
     * Primary key.
     *
     * @var string
     */
    protected $templateName;

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
        return $this->templateName;
    }

    /**
     * Sets the primary key.
     *
     * @param string $templateName the template name
     *
     * @return self
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

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
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}
