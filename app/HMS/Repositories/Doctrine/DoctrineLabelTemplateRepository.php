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
     * Find a temple in the DB.
     *
     * @param string $template_name
     *
     * @return LabelTemplate|null
     */
    public function findOneByTemplateName($template_name)
    {
        return parent::findOneByTemplateName($template_name);
    }

    /**
     * Save temple to the DB.
     *
     * @param LabelTemplate $labelTemplate
     */
    public function save(LabelTemplate $labelTemplate)
    {
        $this->_em->persist($labelTemplate);
        $this->_em->flush();
    }

    /**
     * Remove a temple from the DB.
     *
     * @param LabelTemplate $labelTemplate
     */
    public function remove(LabelTemplate $labelTemplate)
    {
        $this->_em->remove($labelTemplate);
        $this->_em->flush();
    }
}
