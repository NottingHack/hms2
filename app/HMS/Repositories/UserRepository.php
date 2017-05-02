<?php

namespace HMS\Repositories;

use HMS\Entities\User;

interface UserRepository
{
    /**
     * @param  $id
     * @return array
     */
    public function find($id);

    /**
     * @param  string $username
     * @return array
     */
    public function findByUsername(string $username);

    /**
     * @param  string $email
     * @return array
     */
    public function findByEmail(string $email);

    /**
     * @param  string $searchQuery
     * @return array
     */
    public function searchLike(string $searchQuery);

    /**
     * save User to the DB.
     * @param  User $user
     */
    public function save(User $user);

    /**
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
