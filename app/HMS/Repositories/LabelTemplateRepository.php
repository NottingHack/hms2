<?php

namespace HMS\Repositories;

use HMS\Entities\LabelTemplate;

interface LabelTemplateRepository
{
    /**
     * find a temple in the DB.
     * @param  string $template_name
     * @return LabelTemplate|null
     */
    public function findOneByTemplateName($template_name);

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
