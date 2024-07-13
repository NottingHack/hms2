<?php

namespace HMS\Repositories\Doctrine;

use Carbon\Carbon;
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
     * @param string $discordUserSnowflake
     *
     * @return Profile|null
     */
    public function findOneByDiscordUserSnowflake(string $discordUserSnowflake)
    {
        return parent::findOneByDiscordUserSnowflake($discordUserSnowflake);
    }

    /**
     * @param Carbon join date
     *
     * @return Profile|null
     */
    public function findByJoinedOn(Carbon $joinDate)
    {
        $q = parent::createQueryBuilder('profile');

        $q->where('profile.joinDate >= :join_date_lower')
          ->andWhere('profile.joinDate < :join_date_upper');

        $q = $q->setParameter('join_date_lower', $joinDate)
               ->setParameter('join_date_upper', $joinDate->copy()->addDays(1));
        $q = $q->getQuery();

        return $q->getResult();
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
