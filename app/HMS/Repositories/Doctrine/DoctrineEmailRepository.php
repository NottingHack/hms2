<?php

namespace HMS\Repositories\Doctrine;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\Email;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\EmailRepository;

// TODO: findByUserPaginate(????);
class DoctrineEmailRepository extends EntityRepository implements EmailRepository
{
    /**
     * @param int $id
     *
     * @return null|Email
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
    }

    /**
     * @param Role $role
     *
     * @return array
     */
    public function findByRole(Role $role)
    {
        return parent::findByRole($role);
    }

    /**
     * Count emails wait sentAt After date with Subject given.
     *
     * @param Carbon $start
     * @param string $subject
     *
     * @return int
     */
    public function countSentAfterWithSubject(Carbon $start, string $subject): int
    {
        $qb = parent::createQueryBuilder('email')
            ->select('COUNT(email.id)')
            ->where('email.sentAt >= :start')
            ->andWhere('email.subject = :subject');

        $qb->setParameter('start', $start)
            ->setParameter('subject', $subject);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Save Email to the DB.
     *
     * @param Email $email
     */
    public function save(Email $email)
    {
        $this->_em->persist($email);
        $this->_em->flush();
    }
}
