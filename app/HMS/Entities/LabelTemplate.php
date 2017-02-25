<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use App\Http\Requests\LabelTemplateRequest;
use LaravelDoctrine\ORM\Serializers\Arrayable;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class LabelTemplate implements ArrayableContract
{
    use SoftDeletable, Timestampable, Arrayable;

    /**
     * primary key.
     * @var string
     */
    protected $template_name;

    /**
     * @var string
     */
    protected $template;

    /**
     * Update with detials from the LabelTemplateRequest.
     * @param LabelTemplateRequest $request
     * @return self
     */
    public function updateWithRequest(LabelTemplateRequest $request)
    {
        $this->template_name = $request['template_name'];
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
        return $this->template_name;
    }

    /**
     * Sets the primary key.
     *
     * @param string $template_name the template name
     *
     * @return self
     */
    public function setTemplateName($template_name)
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
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}
