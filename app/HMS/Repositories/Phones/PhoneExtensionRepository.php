<?php

namespace HMS\Repositories\Phones;

use HMS\Entities\Phones\PhoneExtension;
use HMS\Entities\User;

interface PhoneExtensionRepository
{
    /**
     * Paginate all extensions.
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return null|PhoneExtension[]
     */
    public function paginateAll($perPage = 100, $pageName = 'page');

    /**
     * Paginate extensions of a specific user.
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return null|PhoneExtension[]
     */
    public function paginateByUser(User $user, $perPage = 10, $pageName = 'page');

    /**
     * Save an extension.
     *
     * @param PhoneExtension $extension
     */
    public function save(PhoneExtension $extension);
}
