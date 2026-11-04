<?php

namespace HMS\Entities\Forms;

use Carbon\Carbon;
use HMS\Entities\User;

class FormResponse
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var User
     */
    protected $responder;

    /**
     * @var array
     */
    protected $responseJson;

    /**
     * @var null|string
     */
    protected $comment;

    /**
     * @var bool
     */
    protected $hidden;

    /**
     * @var Carbon
     */
    protected $createdAt;

    /**
     * @var Carbon
     */
    protected $updatedAt;

    /**
     * Get the form response ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the form response's form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set the form for this response.
     *
     * @param Form $form
     *
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get the user that submitted this response.
     *
     * @return User
     */
    public function getResponder()
    {
        return $this->responder;
    }

    /**
     * Set the user which submitted this response.
     *
     * @param User $responder
     *
     * @return self
     */
    public function setResponder(User $responder)
    {
        $this->responder = $responder;

        return $this;
    }

    /**
     * Get the response.
     *
     * @return array
     */
    public function getResponseJson()
    {
        return $this->responseJson;
    }

    /**
     * Set the response body.
     *
     * @param array $responseJson
     *
     * @return self
     */
    public function setResponseJson($responseJson)
    {
        $this->responseJson = $responseJson;

        return $this;
    }

    /**
     * Get the comment associated with this response.
     *
     * @return null|string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the comment associated with this response.
     *
     * @param string $comment
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Check whether this response has been hidden / archived.
     *
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Hide or unhide this respnse.
     *
     * @param bool $hidden
     *
     * @return self
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get the date/time for when this response was submitted.
     *
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get the date/time for when this response was last updated.
     *
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
