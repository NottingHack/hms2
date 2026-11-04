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
     * @param string $queryString
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateQuery($queryString, $perPage = 15, $pageName = 'page')
    {
        $queryBuilder = $this->createQueryBuilder('products');

        $queryBuilder->where('products.shortDescription like :query');
        $queryBuilder->setParameter('query', '%' . $queryString . '%');
        $query = $queryBuilder->getQuery();

        return $this->paginate($query, $perPage, $pageName);
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
