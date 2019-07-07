<?php

namespace HMS\Repositories\Tools\Doctrine;

use HMS\Entities\Tools\Tool;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Tools\ToolRepository;

class DoctrineToolRepository extends EntityRepository implements ToolRepository
{
    /**
     * @return Tool[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save Tool to the DB.
     *
     * @param Tool $tool
     */
    public function save(Tool $tool)
    {
        $this->_em->persist($tool);
        $this->_em->flush();
    }

    /**
     * Remove Tool from the DB.
     *
     * @param Tool $tool
     */
    public function remove(Tool $tool)
    {
        $this->_em->remove($tool);
        $this->_em->flush();
    }
}
