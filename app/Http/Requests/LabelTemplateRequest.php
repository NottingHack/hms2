<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabelTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('labelTemplate.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'templateName' => 'required',
            'template' => 'required',
        ];

        if ($this->isMethod('post')) {
            $rules['templateName'] = 'required|unique:HMS\Entities\LabelTemplate,templateName';
        }

        return $rules;
    }
}
