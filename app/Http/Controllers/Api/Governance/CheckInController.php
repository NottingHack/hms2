<?php

namespace App\Http\Controllers\Api\Governance;

use App\Events\Governance\AttendeeCheckIn;
use App\Events\Governance\ProxyCheckedIn;
use App\Http\Controllers\Controller;
use HMS\Entities\Gatekeeper\RfidTagState;
use HMS\Entities\Governance\Meeting;
use HMS\Repositories\Gatekeeper\RfidTagRepository;
use HMS\Repositories\Governance\MeetingRepository;
use HMS\Repositories\Governance\ProxyRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

class CheckInController extends Controller
{
    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * @var ProxyRepository
     */
    protected $proxyRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @param MeetingRepository $meetingRepository
     * @param RfidTagRepository $rfidTagRepository
     * @param ProxyRepository $proxyRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        MeetingRepository $meetingRepository,
        RfidTagRepository $rfidTagRepository,
        ProxyRepository $proxyRepository,
        RoleRepository $roleRepository,
        UserRepository $userRepository
    ) {
        $this->meetingRepository = $meetingRepository;
        $this->rfidTagRepository = $rfidTagRepository;
        $this->proxyRepository = $proxyRepository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;

        // $this->middleware('can:governance.meeting.view')->only(['show']); // applied in routes files so only applies when coming from web not client api
        $this->middleware('can:governance.meeting.checkIn')->only(['checkInUser']);
    }

    /**
     * Helper to get meeting counts to be sent as json.
     *
     * @param Meeting $meeting
     *
     * @return array
     */
    protected function perpMeetingData(Meeting $meeting)
    {
        $representedProxies = $this->proxyRepository->countRepresentedForMeeting($meeting);
        $checkInCount = $meeting->getAttendees()->count() + $representedProxies;

        $data = [
            'id' => $meeting->getId(),
            'title' => $meeting->getTitle(),
            'startTime' => $meeting->getStartTime()->toJSON(),
            'extraordinary' => $meeting->isExtraordinary(),
            'currentMembers' => $meeting->getCurrentMembers(),
            'votingMembers' => $meeting->getVotingMembers(),
            'quorum' => $meeting->getQuorum(),
            'attendees' => $meeting->getAttendees()->count(),
            'proxies' => $meeting->getProxies()->count(),
            'representedProxies' => $representedProxies,
            'checkInCount' => $checkInCount,
        ];

        return $data;
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
        return response()->json($this->perpMeetingData($meeting));
    }

    /**
     * Return the next scheduled Meeting.
     *
     * @return \Illuminate\Http\Response
     */
    public function next()
    {
        $meeting = $this->meetingRepository->findNext();

        if (empty($meeting)) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_NOT_FOUND,
                        'title'  => 'Not Found',
                        'detail' => 'The requested resource does not exist.',
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_NOT_FOUND);
        }

        return response()->json($this->perpMeetingData($meeting));
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
            $message = 'Is not allowed to vote. Status: ' . $memberStatus->getDisplayName();
        } elseif ($meeting->getAttendees()->contains($user)) {
            $message = 'Already Checked-in';
        } else {
            $meeting->getAttendees()->add($user);
            $this->meetingRepository->save($meeting);
            $message = 'Checked-in';

            // If this user has designated a proxy, remove it since they are now present
            $_proxy = $this->proxyRepository->findOneByPrincipal($meeting, $user);
            if ($_proxy) {
                $proxy = $_proxy->getProxy(); // the User
                $this->proxyRepository->remove($_proxy);
                event(new ProxyCheckedIn($meeting, $user, $proxy));
                $message .= ', Proxy Cancelled';
            }

            // If this user has communicated their absence, remove it since they are now present
            if ($meeting->getAbsentees()->contains($user)) {
                $meeting->getAbsentees()->remove($_proxy);
            }

            event(new AttendeeCheckIn($meeting, $user, $message));
        }

        $data = $this->perpMeetingData($meeting);
        $data['checkInUser'] = [
            'name' => $user->getFullname(),
            'message' => $message,
        ];

        return response()->json($data);
    }

    /**
     * Check-in a user into a specified meeting using an RfidTag.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function checkInUserByRFID(Request $request, Meeting $meeting)
    {
        if ($meeting->getStartTime()->copy()->endOfDay()->isPast()) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_FORBIDDEN,
                        'title'  => 'Forbidden',
                        'detail' => 'Can not Check-in to a meeting in the past',
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
        }

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

        if ($user->cannot('governance.voting.canVote')) {
            $memberStatus = $this->roleRepository->findMemberStatusForUser($user);
            $message = 'Is not allowed to vote. Status: ' . $memberStatus->getDisplayName();
        } elseif ($meeting->getAttendees()->contains($user)) {
            $message = 'Already Checked-in';
        } else {
            $meeting->getAttendees()->add($user);
            $this->meetingRepository->save($meeting);
            $message = 'Checked-in';

            // If this user has designated a proxy, remove it since they are now present
            $_proxy = $this->proxyRepository->findOneByPrincipal($meeting, $user);
            if ($_proxy) {
                $proxy = $_proxy->getProxy(); // the User
                $this->proxyRepository->remove($_proxy);
                event(new ProxyCheckedIn($meeting, $user, $proxy));
                $message .= ', Proxy Cancelled';
            }

            // If this user has communicated their absence, remove it since they are now present
            if ($meeting->getAbsentees()->contains($user)) {
                $meeting->getAbsentees()->remove($_proxy);
            }

            event(new AttendeeCheckIn($meeting, $user, $message));
        }

        $data = $this->perpMeetingData($meeting);
        $data['checkInUser'] = [
            'name' => $user->getFullname(),
            'message' => $message,
        ];

        return response()->json($data);
    }
}
