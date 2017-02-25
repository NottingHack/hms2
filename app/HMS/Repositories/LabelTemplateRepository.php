<?php

namespace HMS\Repositories;

use HMS\Entities\LabelTemplate;

interface LabelTemplateRepository
{
    /**
     * find a temple in the DB.
     * @param  mixed $template_name
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($template_name);

    /**
     * save temple to the DB.
     * @param  LabelTemplate $labelTemplate
     */
    public function save(LabelTemplate $labelTemplate);

    /**
     * remove a temple from the DB.
     * @param  LabelTemplate $labelTemplate
     */
    public function remove(LabelTemplate $labelTemplate);

    /**
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
