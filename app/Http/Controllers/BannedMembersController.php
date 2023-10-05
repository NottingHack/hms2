<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Repositories\UserRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Support\Facades\Auth;

class BannedMembersController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var RoleUpdateRepository
     */
    protected $roleUpdateRepository;

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param RoleUpdateRepository $roleUpdateRepository
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        RoleUpdateRepository $roleUpdateRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->roleUpdateRepository = $roleUpdateRepository;
    }

    /**
     * Show the list of banned members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bannedRole = $this->roleRepository->findOneByName(Role::MEMBER_BANNED);
        $temporaryBannedRole = $this->roleRepository->findOneByName(Role::MEMBER_TEMPORARYBANNED);

        $bannedUsers = $this->userRepository->paginateUsersWithRole($bannedRole, 100, 'banned');
        $temporaryBannedUsers = $this->userRepository->paginateUsersWithRole($temporaryBannedRole, 100, 'banned');

        $updates = [];

        foreach ($bannedUsers as $user) {
            $update = $this->roleUpdateRepository->findLatestRoleAddedByUser($bannedRole, $user);
            $updates[$user->getId()] = $update;
        }

        foreach ($temporaryBannedUsers as $user) {
            $update = $this->roleUpdateRepository->findLatestRoleAddedByUser($temporaryBannedRole, $user);
            $updates[$user->getId()] = $update;
        }

        return view('members.banned')->with([
            'bannedUsers' => $bannedUsers,
            'temporaryBannedUsers' => $temporaryBannedUsers,
            'updates' => $updates
        ]);
    }
}
