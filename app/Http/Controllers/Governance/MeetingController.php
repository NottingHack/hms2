<?php

namespace App\Http\Controllers\Governance;

use App\Events\Governance\AttendeeCheckIn;
use App\Events\Governance\ProxyCheckedIn;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use HMS\Entities\Governance\Meeting;
use HMS\Factories\Governance\MeetingFactory;
use HMS\Repositories\Governance\MeetingRepository;
use HMS\Repositories\Governance\ProxyRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     * @var MeetingFactory
     */
    protected $meetingFactory;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ProxyRepository
     */
    protected $proxyRepository;

    /**
     * Constructor.
     *
     * @param MeetingRepository $meetingRepository
     * @param MeetingFactory $meetingFactory
     * @param UserRepository $userRepository
     * @param ProxyRepository $proxyRepository
     */
    public function __construct(
        MeetingRepository $meetingRepository,
        MeetingFactory $meetingFactory,
        UserRepository $userRepository,
        ProxyRepository $proxyRepository
    ) {
        $this->meetingRepository = $meetingRepository;
        $this->meetingFactory = $meetingFactory;
        $this->userRepository = $userRepository;
        $this->proxyRepository = $proxyRepository;

        $this->middleware('can:governance.meeting.view')->only(['index', 'show', 'attendees', 'absentees']);
        $this->middleware('can:governance.meeting.create')->only(['create', 'store']);
        $this->middleware('can:governance.meeting.edit')->only(['edit', 'update']);
        $this->middleware('can:governance.meeting.checkIn')->only(['checkIn', 'checkInUser']);
        $this->middleware('can:governance.meeting.recordAbsence')->only(['absence', 'recordAbsence']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = $this->meetingRepository->paginateAll();

        return view('governance.meetings.index')
            ->with('meetings', $meetings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('governance.meetings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'startTime' => 'required|date',
            'extraordinary' => 'sometimes|required',
        ]);

        $meeting = $this->meetingFactory->create(
            $validatedData['title'],
            new Carbon($validatedData['startTime']),
            isset($validatedData['extraordinary']) ? true : false
        );

        $this->meetingRepository->save($meeting);

        flash('Meeting \'' . $meeting->getTitle() . '\' created.')->success();

        return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        $representedProxies = $this->proxyRepository->countRepresentedForMeeting($meeting);
        $checkInCount = $meeting->getAttendees()->count() + $representedProxies;

        return view('governance.meetings.show')
            ->with('meeting', $meeting)
            ->with('representedProxies', $representedProxies)
            ->with('checkInCount', $checkInCount);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {
        return view('governance.meetings.edit')
            ->with('meeting', $meeting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'startTime' => 'required|date',
            'extraordinary' => 'sometimes|required',
        ]);

        $meeting->setTitle($validatedData['title']);
        $meeting->setStartTime(new Carbon($validatedData['startTime']));
        $meeting->setExtraordinary(isset($validatedData['extraordinary']) ? true : false);

        $this->meetingRepository->save($meeting);

        flash('Meeting \'' . $meeting->getTitle() . '\' updated.')->success();

        return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
    }

    /**
     * View list of attendees for a Meeting.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function attendees(Meeting $meeting)
    {
        // TODO: paginateAttendeesForMeeting

        return view('governance.meetings.attendees')
            ->with('meeting', $meeting);
    }

    /**
     * Show the form to check-in a User.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Meeting $meeting)
    {
        if ($meeting->getStartTime()->copy()->endOfDay()->isPast()) {
            flash('Can not Check-in to a meeting in the past')->success();

            return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
        }

        $representedProxies = $this->proxyRepository->countRepresentedForMeeting($meeting);
        $checkInCount = $meeting->getAttendees()->count() + $representedProxies;

        return view('governance.meetings.check_in')
            ->with('meeting', $meeting)
            ->with('representedProxies', $representedProxies)
            ->with('checkInCount', $checkInCount);
    }

    /**
     * Check-in a user into a specified meeting.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function checkInUser(Request $request, Meeting $meeting)
    {
        if ($meeting->getStartTime()->copy()->endOfDay()->isPast()) {
            flash('Can not Check-in to a meeting in the past')->success();

            return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
        }

        $validatedData = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
        ]);

        $user = $this->userRepository->findOneById($validatedData['user_id']);

        if ($user->cannot('governance.voting.canVote')) {
            $memberStatus = $this->roleRepository->findMemberStatusForUser($user);
            flash($user->getFullname() . ' is not allowed to vote. Status: ' . $memberStatus->getDisplayName())
                ->warning();
        } elseif ($meeting->getAttendees()->contains($user)) {
            flash($user->getFullname() . ' already Checked-in.');
        } else {
            $meeting->getAttendees()->add($user);
            $this->meetingRepository->save($meeting);

            flash($user->getFullname() . ' Checked-in.')->success();

            // If this user has designated a proxy, remove it since they are now present
            $_proxy = $this->proxyRepository->findOneByPrincipal($meeting, $user);
            if ($_proxy) {
                $proxy = $_proxy->getProxy(); // the User
                $this->proxyRepository->remove($_proxy);
                event(new ProxyCheckedIn($meeting, $user, $proxy));
                flash('Proxy cancelled');
            }

            // If this user has communicated their absence, remove it since they are now present
            if ($meeting->getAbsentees()->contains($user)) {
                $meeting->getAbsentees()->remove($_proxy);
            }

            event(new AttendeeCheckIn($meeting, $user, 'Checked-in'));
        }

        return redirect()->route('governance.meetings.check-in', ['meeting' => $meeting->getId()]);
    }

    /**
     * View list of absentees for a Meeting.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function absentees(Meeting $meeting)
    {
        // TODO: paginateAbsenteesForMeeting

        return view('governance.meetings.absentees')
            ->with('meeting', $meeting);
    }

    /**
     * Show the form to register a User's absence.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function absence(Meeting $meeting)
    {
        if ($meeting->getStartTime()->isPast()) {
            flash('Can not be absent to a meeting in the past')->success();

            return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
        }

        return view('governance.meetings.absence')
            ->with('meeting', $meeting);
    }

    /**
     * Record a User's absence from a specified meeting.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function recordAbsence(Request $request, Meeting $meeting)
    {
        if ($meeting->getStartTime()->isPast()) {
            flash('Can not be absent to a meeting in the past')->success();

            return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
        }

        $validatedData = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
        ]);

        $user = $this->userRepository->findOneById($validatedData['user_id']);

        if ($user->cannot('governance.voting.canVote')) {
            $memberStatus = $this->roleRepository->findMemberStatusForUser($user);
            flash($user->getFullname() . ' is not allowed to vote. Status: ' . $memberStatus->getDisplayName())
                ->warning();
        } elseif ($meeting->getAbsentees()->contains($user)) {
            flash($user->getFullname() . ' already recorded absent.');
        } else {
            $meeting->getAbsentees()->add($user);
            $this->meetingRepository->save($meeting);

            flash($user->getFullname() . '\'s absence recorded.')->success();
        }

        return redirect()->route('governance.meetings.absence', ['meeting' => $meeting->getId()]);
    }
}
