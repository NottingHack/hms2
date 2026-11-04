<?php

namespace HMS\Repositories\Forms;

use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\User;

interface FormResponseRepository
{
    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * Paginates all forms.
     *
     * @param Form $form
     * @param bool $includeArchived = false
     * @param int $perPage = 15
     * @param string $pageName = 'page'
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateForForm(Form $form, $perPage = 15, $pageName = 'page');

    /**
     * Count the number of responses to a given form by a user.
     *
     * @param Form $form
     * @param User $user
     *
     * @return int
     */
    public function countUserResponsesForForm(Form $form, User $user);

    /**
     * Commit the form response to the database.
     *
     * @param FormResponse $formResponse
     *
     * @return void
     */
    public function save(FormResponse $formResponse);
}
