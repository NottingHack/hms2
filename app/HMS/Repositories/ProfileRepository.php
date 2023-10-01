<?php

namespace HMS\Repositories;

use HMS\Entities\Profile;

interface ProfileRepository
{
    /**
     * Total all negative balances.
     *
     * @return int
     */
    public function totalDebt();

    /**
     * Total all negative balances for Role::MEMEBER_CURENT.
     *
     * @return int
     */
    public function totalDebtForCurrentMembers();

    /**
     * Total all negative balances for Role::MEMEBER_Ex.
     *
     * @return int
     */
    public function totalDebtForExMembers();

    /**
     * Total all posative balances.
     *
     * @return int
     */
    public function totalCredit();

    /**
     * Total all posative balances for Role::MEMEBER_CURENT.
     *
     * @return int
     */
    public function totalCreditForCurrentMembers();

    /**
     * Total all posative balances for Role::MEMEBER_Ex.
     *
     * @return int
     */
    public function totalCreditForExMembers();

    /**
     * @param string $discordUsername
     *
     * @return Profile|null
     */
    public function findOneByDiscordUsername(string $discordUsername);

    /**
     * Save Profile to the DB.
     *
     * @param Profile $profile
     */
    public function save(Profile $profile);
}
