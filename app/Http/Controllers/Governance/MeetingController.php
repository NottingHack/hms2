<?php

namespace App\Http\Controllers\Governance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Entities\Governance\Meeting;
use HMS\Repositories\UserRepository;
use HMS\Factories\Governance\MeetingFactory;
use HMS\Repositories\Governance\ProxyRepository;
use HMS\Repositories\Governance\MeetingRepository;

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

        $this->middleware('can:governance.meeting.view')->only(['index', 'show']);
        $this->middleware('can:governance.meeting.create')->only(['create', 'store']);
        $this->middleware('can:governance.meeting.edit')->only(['edit', 'update']);
        $this->middleware('can:governance.meeting.checkIn')->only(['checkIn', 'checkInUser']);
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
     * @param  Meeting $meeting
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
        if ($meeting->getStartTime()->isPast()) {
            flash('Can not Check-In to a meeting in the past')->success();

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
        if ($meeting->getStartTime()->isPast()) {
            flash('Can not Check-In to a meeting in the past')->success();

            return redirect()->route('governance.meetings.show', ['meeting' => $meeting->getId()]);
        }

        $validatedData = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
        ]);

        $user = $this->userRepository->findOneById($validatedData['user_id']);

        if ($meeting->getAttendees()->contains($user)) {
            flash($user->getFullName() . ' already Checked-In.');
        } else {
            $meeting->getAttendees()->add($user);
            $this->meetingRepository->save($meeting);

            // TODO: if this user has designated a proxy, remove it since they are now present

            flash($user->getFullName() . ' Checked-In.')->success();
        }

        return redirect()->route('governance.meetings.check-in', ['meeting' => $meeting->getId()]);
    }
}
