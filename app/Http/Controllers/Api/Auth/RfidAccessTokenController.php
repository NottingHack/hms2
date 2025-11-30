<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use HMS\Entities\Gatekeeper\RfidTagState;
use HMS\Repositories\Gatekeeper\RfidTagRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

class RfidAccessTokenController extends Controller
{
    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * Create a new controller instance.
     *
     * @param RfidTagRepository $rfidTagRepository
     */
    public function __construct(RfidTagRepository $rfidTagRepository)
    {
        $this->rfidTagRepository = $rfidTagRepository;
    }

    /**
     * Given an rfidSerial issue a Personal Access Token against it's user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'rfidSerial' => [
                'required',
            ],
        ]);

        $rfidTag = $this->rfidTagRepository->findByRfidSerial($validatedData['rfidSerial']);

        if (empty($rfidTag)) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_NOT_FOUND,
                        'title' => 'Not Found',
                        'detail' => 'RFID not found',
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_NOT_FOUND);
        } elseif ($rfidTag->getState() != RfidTagState::ACTIVE) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_FORBIDDEN,
                        'title' => 'Forbidden',
                        'detail' => 'RFID not active',
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
        }

        $user = $rfidTag->getUser();

        // revoke any old tokens
        $tokens = $user->tokens()->where('name', 'Rfid Authorized');
        foreach ($tokens as $token) {
            $token->revoke();
        }

        $token = $user->createToken('Rfid Authorized');
        $response = [
            'token_type' => 'Bearer',
            'expires_in' => (int) abs(Carbon::now()->diffInSeconds($token->token->expires_at)),
            'access_token' => $token->accessToken,
        ];

        $rfidTag->setLastUsed(Carbon::now());
        $this->rfidTagRepository->save($rfidTag);

        return response()->json($response);
    }
}
