<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use HMS\Repositories\InviteRepository;

class SearchController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var InviteRepository
     */
    protected $inviteRepository;

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $userRepository
     * @param InviteRepository $inviteRepository
     */
    public function __construct(
        UserRepository $userRepository,
        InviteRepository $inviteRepository
    ) {
        $this->userRepository = $userRepository;
        $this->inviteRepository = $inviteRepository;

        $this->middleware('can:search.users')->only(['users']);
        $this->middleware('can:search.invites')->only(['invites']);
    }

    /**
     * Search for users.
     *
     * @param string $searchQuery
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function users(string $searchQuery = null, Request $request)
    {
        if ($request['q']) {
            $searchQuery = $request['q'];
        } elseif (is_null($searchQuery)) {
            return response()->json([]);
        }

        // TODO: consider how to paginate response (posible fractal)
        $users = $this->userRepository->searchLike($searchQuery, $request->input('withAccount', null), true, 30);

        $users->setCollection($users->getCollection()->map(function ($user) {
            return [
                'id' => $user->getId(),
                'fullname' => $user->getFullname(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'address1' => $user->getProfile() ? $user->getProfile()->getAddress1() : null,
                'addressPostcode' => $user->getProfile() ? $user->getProfile()->getAddressPostcode() : null,
                'accountId' => $user->getAccount() ? $user->getAccount()->getId() : null,
                'paymentRef' => $user->getAccount() ? $user->getAccount()->getPaymentRef() : '',
            ];
        }));

        return response()->json($users);
    }

    /**
     * Search for Invites.
     *
     * @param string $searchQuery
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function invites(string $searchQuery = null, Request $request)
    {
        if ($request['q']) {
            $searchQuery = $request['q'];
        } elseif (is_null($searchQuery)) {
            return response()->json([]);
        }

        // TODO: consider how to paginate response (posible fractal)
        $invites = $this->inviteRepository->searchLike($searchQuery, true, 30);

        $invites->setCollection($invites->getCollection()->map(function ($invite) {
            return [
                'id' => $invite->getId(),
                'email' => $invite->getEmail(),
            ];
        }));

        return response()->json($invites);
    }
}
