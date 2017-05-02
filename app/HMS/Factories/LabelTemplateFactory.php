<?php

namespace HMS\Factories;

use Carbon\Carbon;
use HMS\Entities\LabelTemplate;
use App\Http\Requests\LabelTemplateRequest;

class LabelTemplateFactory
{
    /**
     * Function to instantiate a new LabelTemplate from given params.
     *
     * @param string $templateName
     * @param string $template
     * @return  LabelTemplate
     */
    public function create($templateName, $template)
    {
        $_labelTemplate = new LabelTemplate();
        $_labelTemplate->setTemplateName($templateName);
        $_labelTemplate->setTemplate($template);
        $now = Carbon::now();
        $_labelTemplate->setCreatedAt($now);
        $_labelTemplate->setUpdatedAt(clone $now);

        return $_labelTemplate;
    }

    /**
     * Function to instantiate a new LabelTemplate from a LabelTemplateRequest.
     *
     * @param LabelTemplateRequest $request
     * @return  LabelTemplate
     */
    public function createFromRequest(LabelTemplateRequest $request)
    {
        $_labelTemplate = new LabelTemplate();
        $_labelTemplate->setTemplateName($request['templateName']);
        $_labelTemplate->setTemplate($request['template']);
        $now = Carbon::now();
        $_labelTemplate->setCreatedAt($now);
        $_labelTemplate->setUpdatedAt(clone $now);

        return $_labelTemplate;
    }
}
