<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use HMS\Repositories\InviteRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        $this->middleware('canAny:search.users,tools.search.users')->only(['users']);
        $this->middleware('can:search.invites')->only(['invites']);
    }

    /**
     * Search for users.
     *
     * @param Request $request
     * @param string $searchQuery
     *
     * @return \Illuminate\Http\Response
     */
    public function users(Request $request, string $searchQuery = null)
    {
        if ($request['q']) {
            $searchQuery = $request['q'];
        } elseif (is_null($searchQuery)) {
            return response()->json([]);
        }

        // force currentOnly for search.users.tools only access
        if (Gate::allows('tools.search.users') && Gate::denies('search.users')) {
            $currentOnly = true;
        } else {
            $currentOnly = $request->input('currentOnly', false);
        }

        // TODO: consider how to paginate response (posible fractal)
        $users = $this->userRepository->searchLike(
            $searchQuery,
            $request->input('withAccount', false),
            $currentOnly,
            true,
            30
        );

        $users->setCollection($users->getCollection()->map(function ($user) {
            $ret = [
                'id' => $user->getId(),
                'fullname' => $user->getFullname(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'accountId' => $user->getAccount() ? $user->getAccount()->getId() : null,
                'paymentRef' => $user->getAccount() ? $user->getAccount()->getPaymentRef() : '',
            ];

            if (Gate::allows('profile.view.all')) {
                $ret = array_merge($ret, [
                    'address1' => $user->getProfile() ? $user->getProfile()->getAddress1() : null,
                    'addressPostcode' => $user->getProfile() ? $user->getProfile()->getAddressPostcode() : null,
                ]);
            }

            return $ret;
        }));

        return response()->json($users);
    }

    /**
     * Search for Invites.
     *
     * @param Request $request
     * @param string $searchQuery
     *
     * @return \Illuminate\Http\Response
     */
    public function invites(Request $request, string $searchQuery = null)
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
