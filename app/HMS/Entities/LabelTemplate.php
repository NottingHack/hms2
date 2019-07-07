<?php

namespace HMS\Entities;

use HMS\Traits\Entities\Arrayable;
use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use App\Http\Requests\LabelTemplateRequest;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class LabelTemplate implements ArrayableContract
{
    use SoftDeletable, Timestampable, Arrayable;

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
     * Update with detials from the LabelTemplateRequest.
     *
     * @param LabelTemplateRequest $request
     *
     * @return self
     */
    public function updateWithRequest(LabelTemplateRequest $request)
    {
        $this->templateName = $request['templateName'];
        $this->template = $request['template'];

        return $this;
    }

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
