<?php

namespace HMS\Repositories;

use HMS\Entities\Role;
use HMS\Entities\User;

interface UserRepository
{
    /**
     * @param $id
     *
     * @return null|User
     */
    public function findOneById($id);

    /**
     * @param string $username
     *
     * @return array
     */
    public function findByUsername(string $username);

    /**
     * @param string $email
     *
     * @return array
     */
    public function findByEmail(string $email);

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findOneByEmail(string $email);

    /**
     * Find Voting members base on Physical access in last six months.
     *
     * @return User[]
     */
    public function findVotingPhysical();

    /**
     * Find stated Voting members in last six months.
     *
     * @return User[]
     */
    public function findVotingStated();

    /**
     * Find stated Non-voting members in last six months.
     *
     * @return User[]
     */
    public function findNonVotingStated();

    /**
     * Count Current Members.
     *
     * @return int
     */
    public function countCurrentMembers(): int;

    /**
     * @param string $searchQuery
     * @param bool $hasAccount limit to users with associated accounts
     * @param bool $currentOnly limit to only MEMBER_CURRENT users
     * @param bool $paginate
     * @param int $perPage
     * @param string $pageName
     *
     * @return User[]|array|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchLike(
        string $searchQuery,
        bool $hasAccount = false,
        bool $currentOnly = false,
        bool $paginate = false,
        $perPage = 15,
        $pageName = 'page'
    );

    /**
     * Save User to the DB.
     *
     * @param User $user
     */
    public function save(User $user);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * @param Role $role
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateUsersWithRole(Role $role, $perPage = 15, $pageName = 'page');
}
