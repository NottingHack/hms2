<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;

class SearchController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->middleware('can:search.users')->only(['users']);
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
                'paymentRef' => $user->getAccount() ? $user->getAccount()->getPaymentRef() : null,
            ];
        }));

        return response()->json($users);
    }
}
