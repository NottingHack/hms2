<?php

namespace HMS\Repositories\Phones\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Phones\PhoneExtension;
use HMS\Entities\Phones\ExtensionType;
use HMS\Entities\User;
use HMS\Repositories\Phones\PhoneExtensionRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrinePhoneExtensionRepository extends EntityRepository implements PhoneExtensionRepository
{
    use PaginatesFromRequest;

    /**
     * Paginate all extensions
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return null|PhoneExtension[]
     */
    public function paginateAll($perPage = 100, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('phone_extensions');
        $q = $q->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Paginate extensions of a specific user
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return null|PhoneExtension[]
     */
    public function paginateByUser(User $user, $perPage = 10, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('phone_extensions')
                   ->where('phone_extensions.user = :user_id');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Save an extension
     *
     * @param PhoneExtension $extension
     *
     */
    public function save(PhoneExtension $extension)
    {
        $this->_em->persist($extension);
        $this->_em->flush();
    }
}
