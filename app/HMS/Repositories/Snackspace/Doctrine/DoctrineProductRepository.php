<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\Product;
use HMS\Repositories\Snackspace\ProductRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineProductRepository extends EntityRepository implements ProductRepository
{
    use PaginatesFromRequest;

    /**
     * Find a Product.
     *
     * @param int $id
     *
     * @return null|Product
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return Product[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save Product to the DB.
     *
     * @param Product $product
     */
    public function save(Product $product)
    {
        $this->_em->persist($product);
        $this->_em->flush();
    }
}
