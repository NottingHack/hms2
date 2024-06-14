<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Profile;
use HMS\Entities\Role;
use HMS\Repositories\ProfileRepository;

class DoctrineProfileRepository extends EntityRepository implements ProfileRepository
{
    /**
     * Total all negative balances.
     *
     * @return int
     */
    public function totalDebt()
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.balance < 0')
            ->select('SUM(profile.balance) as total_debt');

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Total all negative balances for Role::MEMEBER_CURENT.
     *
     * @return int
     */
    public function totalDebtForCurrentMembers()
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.balance < 0')
            ->leftJoin('profile.user', 'user')->addSelect('user')
            ->leftJoin('user.roles', 'role')->addSelect('role')
            ->andWhere('role.name = :role_name')
            ->select('SUM(profile.balance) as total_debt');

        $q = $q->setParameter('role_name', Role::MEMBER_CURRENT);

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Total all negative balances for Role::MEMEBER_Ex.
     *
     * @return int
     */
    public function totalDebtForExMembers()
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.balance < 0')
            ->leftJoin('profile.user', 'user')
            ->leftJoin('user.roles', 'role')
            ->andWhere('role.name = :role_name')
            ->select('SUM(profile.balance) as total_debt');

        $q = $q->setParameter('role_name', Role::MEMBER_EX);

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Total all posative balances.
     *
     * @return int
     */
    public function totalCredit()
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.balance > 0')
            ->select('SUM(profile.balance) as total_debt');

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Total all posative balances for Role::MEMEBER_CURENT.
     *
     * @return int
     */
    public function totalCreditForCurrentMembers()
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.balance > 0')
            ->leftJoin('profile.user', 'user')->addSelect('user')
            ->leftJoin('user.roles', 'role')->addSelect('role')
            ->andWhere('role.name = :role_name')
            ->select('SUM(profile.balance) as total_debt');

        $q = $q->setParameter('role_name', Role::MEMBER_CURRENT);

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Total all posative balances for Role::MEMEBER_Ex.
     *
     * @return int
     */
    public function totalCreditForExMembers()
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.balance > 0')
            ->leftJoin('profile.user', 'user')
            ->leftJoin('user.roles', 'role')
            ->andWhere('role.name = :role_name')
            ->select('SUM(profile.balance) as total_debt');

        $q = $q->setParameter('role_name', Role::MEMBER_EX);

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * @param string $discordUsername
     *
     * @return Profile|null
     */
    public function findOneByDiscordUsername(string $discordUsername)
    {
        return parent::findOneByDiscordUsername($discordUsername);
    }

    /**
     * Save Profile to the DB.
     *
     * @param Profile $profile
     */
    public function save(Profile $profile)
    {
        $this->_em->persist($profile);
        $this->_em->flush();
    }
}
