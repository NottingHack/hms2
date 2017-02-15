<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Repositories\LabelTemplateRepository;

class DoctrineLabelTemplateRepository extends EntityRepository implements LabelTemplateRepository
{
    /**
     * find a temple in the DB.
     * @param  mixed $template_name
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($template_name)
    {
        return parent::find($template_name);
    }
}
