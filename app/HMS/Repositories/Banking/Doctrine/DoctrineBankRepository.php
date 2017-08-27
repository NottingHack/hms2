<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Bank;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Banking\BankRepository;

class DoctrineBankRepository extends EntityRepository implements BankRepository
{
    /**
     * @param  $id
     * @return Bank[]
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * save Bank to the DB.
     * @param  Bank $bank
     */
    public function save(Bank $bank)
    {
        $this->_em->persist($bank);
        $this->_em->flush();
    }
}
