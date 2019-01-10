<?php

namespace App\Http\Controllers\GateKeeper;

use HMS\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use HMS\Entities\GateKeeper\Pin;
use App\Http\Controllers\Controller;
use HMS\Entities\GateKeeper\RfidTag;
use HMS\Repositories\UserRepository;
use HMS\Entities\GateKeeper\PinState;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Entities\GateKeeper\RfidTagState;
use HMS\Repositories\GateKeeper\PinRepository;
use HMS\Repositories\GateKeeper\RfidTagRepository;

class RfidTagsController extends Controller
{
    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var PinRepository
     */
    protected $pinRepository;

    /**
     * Create a new controller instance.
     * @param RfidTagRepository $rfidTagRepository
     * @param UserRepository    $userRepository
     * @param PinRepository     $pinRepository
     */
    public function __construct(RfidTagRepository $rfidTagRepository, UserRepository $userRepository, PinRepository $pinRepository)
    {
        $this->rfidTagRepository = $rfidTagRepository;
        $this->userRepository = $userRepository;
        $this->pinRepository = $pinRepository;

        $this->middleware('can:rfidTags.view.self')->only(['index']);
        $this->middleware('can:rfidTags.edit.self')->only(['edit', 'update']);
        $this->middleware('can:rfidTags.destroy')->only(['destroy']);
        $this->middleware('can:pins.reactivate')->only(['reactivatePin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->findOneById($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != \Auth::user() && \Gate::denies('rfidTags.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
        }

        $rfidTags = $this->rfidTagRepository->paginateByUser($user, 10);
        $pins = $this->pinRepository->findByUser($user);

        return view('gateKeeper.rfidTags.index')
            ->with(['user' => $user, 'rfidTags' => $rfidTags, 'pins' => $pins]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  RfidTag  $rfidTag
     * @return \Illuminate\Http\Response
     */
    public function edit(RfidTag $rfidTag)
    {
        if ($rfidTag->getUser() != \Auth::user() && \Gate::denies('rfidTags.edit.all')) {
            flash('Unauthorized', 'error');

            return redirect()->route('home');
        }

        return view('gateKeeper.rfidTags.edit')
            ->with('rfidTag', $rfidTag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  RfidTag  $rfidTag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RfidTag $rfidTag)
    {
        if ($rfidTag->getUser() != \Auth::user() && \Gate::denies('rfidTags.edit.all')) {
            flash('Unauthorized', 'error');

            return redirect()->route('home');
        }

        // validation
        $this->validate($request, [
            'friendlyName' => 'nullable|string|max:128',
            'state' => [
                'required',
                Rule::in(array_keys(RfidTagState::STATE_STRINGS)),
            ],
        ]);

        // save
        $rfidTag->setFriendlyName($request->friendlyName);
        $rfidTag->setState($request->state);
        $this->rfidTagRepository->save($rfidTag);

        return redirect()->route('rfid-tags.index', ['user' => $rfidTag->getUser()->getId()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RfidTag  $rfidTag
     * @return \Illuminate\Http\Response
     */
    public function destroy(RfidTag $rfidTag)
    {
        $this->rfidTagRepository->remove($rfidTag);

        flash()->success('RfidTag \''.$rfidTag->getBestRfidSerial().'\' removed.');

        return redirect()->route('rfid-tags.index', ['user' => $rfidTag->getUser()->getId()]);
    }

    /**
     * Reactivate a Pin for gatekeeper rfid card enrolment.
     *
     * @param  Pin    $pin
     * @return \Illuminate\Http\Response
     */
    public function reactivatePin(Pin $pin)
    {
        if ($pin->getState() != PinState::CANCELLED) {
            flash('Pin state can not be changed')->error();

            return redirect()->route('rfid-tags.index', ['user' => $pin->getUser()->getId()]);
        }

        $pin->setStateEnroll();
        $this->pinRepository->save($pin);

        flash()->success('Pin \''.$pin->getPin().'\' set to Enrol');

        return redirect()->route('rfid-tags.index', ['user' => $pin->getUser()->getId()]);
    }
}
