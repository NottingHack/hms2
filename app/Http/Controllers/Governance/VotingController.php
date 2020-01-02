<?php

namespace App\Http\Controllers\Governance;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use HMS\Governance\VotingManager;
use App\Http\Controllers\Controller;
use HMS\Governance\VotingPreference;
use HMS\Repositories\UserRepository;

class VotingController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var VotingManager
     */
    protected $votingManager;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository
     * @param VotingManager $votingManager
     */
    public function __construct(
        UserRepository $userRepository,
        VotingManager $votingManager
    ) {
        $this->userRepository = $userRepository;
        $this->votingManager = $votingManager;

        $this->middleware('can:governance.voting.canVote')->only(['index', 'update']);
    }

    /**
     * Display the users voting status.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        $votingStatus = $this->votingManager->getVotingStatusForUser($user);
        $votingPreference = $user->getProfile()->getVotingPreference();
        $votingPreferenceString = $user->getProfile()->getVotingPreferenceString();
        $votingPreferenceStatedAt = $user->getProfile()->getVotingPreferenceStatedAt();

        return view('governance.voting.index')
            ->with([
                'user' => $user,
                'votingStatus' => $votingStatus,
                'votingPreference' => $votingPreference,
                'votingPreferenceString' => $votingPreferenceString,
                'votingPreferenceStatedAt' => $votingPreferenceStatedAt,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'preference' => [
                'required',
                Rule::in(array_keys(VotingPreference::STATE_STRINGS)),
            ],
        ]);

        $user = \Auth::user();

        $user->getProfile()->setVotingPreference($validatedData['preference']);

        $this->userRepository->save($user);

        flash('Preference updated.')->success();

        return redirect()->route('governance.voting.index');
    }
}
