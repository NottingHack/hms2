<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\LabelTemplate;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\LabelTemplateRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineLabelTemplateRepository extends EntityRepository implements LabelTemplateRepository
{
    use PaginatesFromRequest;

    /**
     * find a temple in the DB.
     * @param  mixed $template_name
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findByTemplateName($template_name)
    {
        return parent::find($template_name);
    }

    /**
     * save temple to the DB.
     * @param  LabelTemplate $labelTemplate
     */
    public function save(LabelTemplate $labelTemplate)
    {
        $this->_em->persist($labelTemplate);
        $this->_em->flush();
    }

    /**
     * remove a temple from the DB.
     * @param  LabelTemplate $labelTemplate
     */
    public function remove(LabelTemplate $labelTemplate)
    {
        $this->_em->remove($labelTemplate);
        $this->_em->flush();
    }
}
