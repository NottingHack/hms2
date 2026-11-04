<?php

namespace App\Events\Forms;

use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\Role;
use Illuminate\Queue\SerializesModels;

class FormResponseReceived
{
    use SerializesModels;

    /**
     * @var Form
     */
    public $form;

    /**
     * @var FormResponse
     */
    public $formResponse;

    /**
     * @var Role
     */
    public $role;

    /**
     * Create a new event instance.
     *
     * @param Form $form
     * @param FormResponse $formResponse
     *
     * @return void
     */
    public function __construct(Form $form, FormResponse $formResponse)
    {
        $this->form = $form;
        $this->formResponse = $formResponse;
    }

    /**
     * Set the role for this notification.
     *
     * Hypothetically, other notification targets are supported, so this is optional.
     *
     * @param Role $role
     *
     * @return self
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }
}
