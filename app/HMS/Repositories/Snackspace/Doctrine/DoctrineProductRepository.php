<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use HMS\Entities\Snackspace\Product;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Snackspace\ProductRepository;

class DoctrineProductRepository extends EntityRepository implements ProductRepository
{
    /**
     * save Product to the DB. 
     * @param  Product $product
     */
    public function save(Product $product)
    {
        $this->_em->persist($product);
        $this->_em->flush();
    }
}
