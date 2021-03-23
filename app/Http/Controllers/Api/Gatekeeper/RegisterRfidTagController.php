<?php

namespace App\Http\Controllers\Api\Gatekeeper;

use App\Http\Controllers\Controller;
use App\Http\Resources\Gatekeeper\RfidTag as RfidTagResource;
use HMS\Entities\Gatekeeper\PinState;
use HMS\Factories\Gatekeeper\RfidTagFactory;
use HMS\Repositories\Gatekeeper\PinRepository;
use HMS\Repositories\Gatekeeper\RfidTagRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;

class RegisterRfidTagController extends Controller
{
    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * @var RfidTagFactory
     */
    protected $rfidTagFactory;

    /**
     * @var PinRepository
     */
    protected $pinRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param RfidTagRepository $rfidTagRepository
     * @param RfidTagFactory $rfidTagFactory
     * @param PinRepository $pinRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        RfidTagRepository $rfidTagRepository,
        RfidTagFactory $rfidTagFactory,
        PinRepository $pinRepository,
        UserRepository $userRepository
    ) {
        $this->rfidTagRepository = $rfidTagRepository;
        $this->rfidTagFactory = $rfidTagFactory;
        $this->pinRepository = $pinRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => 'required_without_all:pin|exists:HMS\Entities\User,id',
            'pin' => 'required_without_all:userId|exists:HMS\Entities\Gatekeeper\Pin,pin,state,' . PinState::ENROLL,
            'rfidSerial' => 'required|unique:HMS\Entities\Gatekeeper\RfidTag',
        ]);

        if (array_key_exists('userId', $validatedData)) {
            $user = $this->userRepository->findOneById($validatedData['userId']);
        } else {
            $pin = $this->pinRepository->findOneByPin($validatedData['pin']);
            $pin->setStateCancelled();
            $this->pinRepository->save($pin);
            $user = $pin->getUser();
        }

        $rfidTag = $this->rfidTagFactory->create($user, $validatedData['rfidSerial']);
        $this->rfidTagRepository->save($rfidTag);

        return new RfidTagResource($rfidTag);
    }
}
