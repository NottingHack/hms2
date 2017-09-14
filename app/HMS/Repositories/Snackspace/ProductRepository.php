<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\Product;
use Doctrine\Common\Collections\Collection;

interface ProductRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Product[]
     */
    public function findAll();

    /**
     * save Product to the DB.
     * @param  Product $product
     */
    public function save(Product $product);
}
