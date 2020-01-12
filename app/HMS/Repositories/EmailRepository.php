<?php

namespace HMS\Repositories;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\Email;

// TODO: findByUserPaginate(????);
interface EmailRepository
{
    /**
     * @param $id
     *
     * @return null|Email
     */
    public function findOneById($id);

    /**
     * @param Role $role
     *
     * @return array
     */
    public function findByRole(Role $role);

    /**
     * Count emails wait sentAt After date with Subject given.
     *
     * @param Carbon $start
     * @param string $subject
     *
     * @return int
     */
    public function countSentAfterWithSubject(Carbon $start, string $subject): int;

    /**
     * Save Email to the DB.
     *
     * @param Email $email
     */
    public function save(Email $email);
}
