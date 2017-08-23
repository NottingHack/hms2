<?php

namespace App\Http\Controllers;

use HMS\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use HMS\Entities\GateKeeper\Pin;
use HMS\Entities\GateKeeper\RfidTag;
use HMS\Repositories\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
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
     * @return \Illuminate\Http\Response
     */
    public function index($user = null)
    {
        if (is_null($user)) {
            $_user = \Auth::user();
        } else {
            $_user = $this->userRepository->find($user);
            if (is_null($_user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $user]);
            }
        }

        if ($_user != \Auth::user() && \Gate::denies('rfidTags.view.all')) {
            flash('Unauthorized', 'error');

            return redirect()->route('home');
        }

        $rfidTags = $this->rfidTagRepository->paginateByUser($_user, 10);
        $pins = $this->pinRepository->findByUser($_user);

        return view('rfidTags.index')
            ->with(['user' => $_user, 'rfidTags' => $rfidTags, 'pins' => $pins]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  RfidTag  $rfid_tag
     * @return \Illuminate\Http\Response
     */
    public function edit(RfidTag $rfid_tag)
    {
        if ($rfid_tag->getUser() != \Auth::user() && \Gate::denies('rfidTags.edit.all')) {
            flash('Unauthorized', 'error');

            return redirect()->route('home');
        }

        return view('rfidTags.edit')
            ->with('rfidTag', $rfid_tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  RfidTag  $rfid_tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RfidTag $rfid_tag)
    {
        if ($rfid_tag->getUser() != \Auth::user() && \Gate::denies('rfidTags.edit.all')) {
            flash('Unauthorized', 'error');

            return redirect()->route('home');
        }

        // validation
        $this->validate($request, [
            'friendlyName' => 'nullable|string|max:128',
            'state' => [
                'required',
                Rule::in(array_keys($rfid_tag->statusStrings)),
            ],
        ]);

        // save
        $rfid_tag->setFriendlyName($request->friendlyName);
        $rfid_tag->setState($request->state);
        $this->rfidTagRepository->save($rfid_tag);

        return redirect()->route('rfid_tags.index', [$rfid_tag->getUser()->getId()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RfidTag  $rfid_tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(RfidTag $rfid_tag)
    {
        $this->rfidTagRepository->remove($rfid_tag);

        flash()->success('RfidTag \''.$rfid_tag->getBestRfidSerial().'\' removed.');

        return redirect()->route('rfid_tags.index', ['user' => $rfid_tag->getUser()->getId()]);
    }

    /**
     * Reactivate a Pin for gatekeeper rfid card enrolment.
     *
     * @param  Pin    $pin
     * @return \Illuminate\Http\Response
     */
    public function reactivatePin(Pin $pin)
    {
        if ($pin->getState() != Pin::STATE_CANCELLED) {
            flash('Pin state can not be changed')->error();

            return redirect()->route('rfid_tags.index', ['user' => $pin->getUser()->getId()]);
        }

        $pin->setStateEnrol();
        $this->pinRepository->save($pin);

        flash()->success('Pin \''.$pin->getPin().'\' set to Enrol');

        return redirect()->route('rfid_tags.index', ['user' => $pin->getUser()->getId()]);
    }
}
