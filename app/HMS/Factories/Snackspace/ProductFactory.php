<?php

namespace HMS\Factories\Snackspace;

use HMS\Entities\Snackspace\Product;

class ProductFactory
{
    /**
     * Function to instantiate a new Product from given params.
     *
     * @param int $price
     * @param string $shortDescription
     * @param string|null $longDescription
     *
     * @return Product
     */
    public function create(int $price, string $shortDescription, ?string $longDescription = null)
    {
        $_product = new Product();
        $_product->setPrice($price);
        $_product->setShortDescription($shortDescription);
        $_product->setLongDescription($longDescription);
        $_product->setAvailable(Product::AVAILABLE);

        return $_product;
    }
}
