<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Entities\GateKeeper\RfidTagState;
use HMS\Repositories\GateKeeper\RfidTagRepository;
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
    public function issueRfidToken(Request $request)
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
                        'title'  => 'Not Found',
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
                        'title'  => 'Forbidden',
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
            'token_type' =>'Bearer',
            'expires_in' => Carbon::now()->diffInSeconds($token->token->expires_at),
            'access_token' => $token->accessToken,
            'user_id' => $user->getId(),
        ];

        return response()->json($response);
    }
}
