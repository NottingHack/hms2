<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\Product;

interface ProductRepository
{
    /**
     * save Product to the DB.
     * @param  Product $product
     */
    public function save(Product $product);
}
