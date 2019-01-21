<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\Product;

interface ProductRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Product[]
     */
    public function findAll();

    /**
     * Save Product to the DB.
     *
     * @param Product $product
     */
    public function save(Product $product);
}
