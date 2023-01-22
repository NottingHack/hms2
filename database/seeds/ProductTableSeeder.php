<?php

namespace Database\Seeders;

use HMS\Entities\Snackspace\Product;
use HMS\Repositories\Snackspace\ProductRepository;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new TableSeeder instance.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            ['price' => 50, 'name' => '50p Confectionary'],
            ['price' => 25, 'name' => '25p Confectionary'],
            ['price' => 20, 'name' => 'Crisps 20p'],
            ['price' => 60, 'name' => 'Crisps 60p'],
            ['price' => 50, 'name' => 'Can drink 50p'],
            ['price' => 20, 'name' => '20p Confectionary'],
            ['price' => 10, 'name' => '10p Confectionary'],
            ['price' => 100, 'name' => '£1 Confectionery'],
            ['price' => 60, 'name' => '60p Confectionery'],
        ];

        foreach ($products as $product) {
            $p = new Product();
            $p->setAvailable(1);
            $p->setPrice($product['price']);
            $p->setShortDescription($product['name']);
            $p->setLongDescription($product['name']);

            $this->productRepository->save($p);
        }
    }
}
