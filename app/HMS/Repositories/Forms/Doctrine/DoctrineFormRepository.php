<?php

namespace HMS\Repositories\Forms\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Forms\Form;
use HMS\Repositories\Forms\FormRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineFormRepository extends EntityRepository implements FormRepository
{
    use PaginatesFromRequest;

    /**
     * Save an instance of Form.
     *
     * @param Form $form
     *
     * @return void
     */
    public function save(Form $form)
    {
        $this->_em->persist($form);
        $this->_em->flush();
    }
}
