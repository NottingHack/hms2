<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\Product;

interface ProductRepository
{
    /**
     * Find a Product.
     *
     * @param $id
     *
     * @return null|Product
     */
    public function findOneById($id);

    /**
     * Finds all entities in the repository.
     *
     * @return Product[]
     */
    public function findAll();

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * Save Product to the DB.
     *
     * @param Product $product
     */
    public function save(Product $product);
}
