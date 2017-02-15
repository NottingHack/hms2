<?php

namespace HMS\Repositories;

interface LabelTemplateRepository
{
    /**
     * find a temple in the DB.
     * @param  mixed $template_name
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($template_name);
}
