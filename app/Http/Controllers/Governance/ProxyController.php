<?php

namespace App\Http\Controllers\Governance;

use App\Events\Governance\ProxyCancelled;
use App\Events\Governance\ProxyRegistered;
use App\Events\Governance\ProxyUpdated;
use App\Http\Controllers\Controller;
use HMS\Entities\Governance\Meeting;
use HMS\Entities\User;
use HMS\Factories\Governance\MeetingFactory;
use HMS\Factories\Governance\ProxyFactory;
use HMS\Repositories\Governance\MeetingRepository;
use HMS\Repositories\Governance\ProxyRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProxyController extends Controller
{
    /**
     * @var ProxyRepository
     */
    protected $proxyRepository;

    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var MeetingFactory
     */
    protected $meetingFactory;

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * Constructor.
     *
     * @param ProxyRepository $proxyRepository
     * @param MeetingRepository $meetingRepository
     * @param UserRepository $userRepository
     * @param MeetingFactory $meetingFactory
     * @param ProxyFactory $proxyFactory
     */
    public function __construct(
        ProxyRepository $proxyRepository,
        MeetingRepository $meetingRepository,
        UserRepository $userRepository,
        MeetingFactory $meetingFactory,
        ProxyFactory $proxyFactory
    ) {
        $this->proxyRepository = $proxyRepository;
        $this->meetingRepository = $meetingRepository;
        $this->userRepository = $userRepository;
        $this->meetingFactory = $meetingFactory;
        $this->proxyFactory = $proxyFactory;

        $this->middleware('can:governance.meeting.view')->only(['index']);
        $this->middleware('can:governance.proxy.designateProxy')->only(['designateLink']);
        $this->middleware('can:governance.proxy.representPrincipal')
            ->only(['indexForUser', 'designate', 'store']);
        $this->middleware('canAny:governance.proxy.designateProxy,governance.proxy.representPrincipal')
            ->only(['destroy']);
    }

    /**
     * View all Proxies for a Meeting.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Meeting $meeting)
    {
        // TODO: paginateProxiesForMeeting

        return view('governance.proxies.index')
            ->with('meeting', $meeting);
    }

    /**
     * View Principals for a Proxy (User).
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function indexForUser(Meeting $meeting)
    {
        $user = Auth::user();
        $proxies = $this->proxyRepository->findByProxy($meeting, $user);

        return view('governance.proxies.index_for_user')
            ->with('meeting', $meeting)
            ->with('proxies', $proxies);
    }

    /**
     * Generate a link to pass on your proxy.
     *
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function designateLink(Meeting $meeting)
    {
        $user = Auth::user();
        $proxy = $this->proxyRepository->findOneByPrincipal($meeting, $user);

        return view('governance.proxies.designate_link')
            ->with('user', $user)
            ->with('meeting', $meeting)
            ->with('proxy', $proxy);
    }

    /**
     * Show proxy confirmation form.
     *
     * @param Meeting $meeting
     * @param User $principal
     *
     * @return \Illuminate\Http\Response
     */
    public function designate(Meeting $meeting, User $principal)
    {
        if ($principal->cannot('governance.proxy.designateProxy')) {
            flash($principal->getFullname() . ' is not currently allowed to give away their Proxy')->error();

            return redirect()->route('home');
        }

        if ($principal == Auth::user()) {
            flash('You can not Proxy for yourself')->error();

            return redirect()->route('governance.proxies.link', ['meeting' => $meeting->getId()]);
        }

        // if they have already check in then they can not give away there proxy
        if ($meeting->getAttendees()->contains($principal)) {
            flash($principal->getFullname() . ' already Checked-in at the meeting.')->warning();

            return redirect()->route('home');
        }

        $proxy = $this->proxyRepository->findOneByPrincipal($meeting, Auth::user());

        if (isset($proxy)) {
            flash('You have already given your proxy to ' . $proxy->getProxy()->getFullname() . '. You can not accept someone else\'s Proxy')->error();

            return redirect()->route('governance.proxies.link', ['meeting' => $meeting->getId()]);
        }

        return view('governance.proxies.designate')
            ->with('meeting', $meeting)
            ->with('principal', $principal);
    }

    /**
     * Accept a Designation.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Meeting $meeting)
    {
        $validatedData = $request->validate([
            'principal_id' => [
                'required',
                'exists:HMS\Entities\User,id',
                function ($attribute, $value, $fail) {
                    if ($value == Auth::user()->getId()) {
                        $fail('You can not Proxy for yourself');
                    }
                },
            ],
            'proxy' => 'accepted',
        ]);

        $user = Auth::user();
        $principal = $this->userRepository->find($validatedData['principal_id']);

        // See if a Proxy for this principal all ready exists?
        $_proxy = $this->proxyRepository->findOneByPrincipal($meeting, $principal);

        if (empty($_proxy)) {
            // if no Proxy registers for this Principal, register the new Proxy and notify
            $_proxy = $this->proxyFactory->create($meeting, $user, $principal);
            $this->proxyRepository->save($_proxy);

            event(new ProxyRegistered($_proxy));
        } elseif (isset($_proxy) && $_proxy->getProxy() != $user) {
            // if Proxy.Proxy is for a different User than currently logged in, update it and notify
            $oldProxy = $_proxy->getProxy();
            $_proxy->setProxy($user);
            $this->proxyRepository->save($_proxy);

            event(new ProxyUpdated($_proxy, $oldProxy));
        }

        flash('Proxy for ' . $principal->getFullname() . ' accepted.')->success();

        return redirect()->route('governance.proxies.index-for-user', ['meeting' => $meeting->getId()]);
    }

    /**
     * Remove a proxy.
     * If no Principal is given in request then remove current users Proxy.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Meeting $meeting)
    {
        $validatedData = $request->validate([
            'principal_id' => [
                'exists:HMS\Entities\User,id',
            ],
        ]);

        if (array_key_exists('principal_id', $validatedData)) {
            $principal = $this->userRepository->find($validatedData['principal_id']);
        } else {
            $principal = Auth::user();
        }

        // See if a Proxy for this principal all ready exists?
        $_proxy = $this->proxyRepository->findOneByPrincipal($meeting, $principal); // the Proxy
        $proxy = $_proxy->getProxy(); // the User

        $this->proxyRepository->remove($_proxy);

        event(new ProxyCancelled($meeting, $principal, $proxy));

        flash('Proxy cancelled')->success();

        return redirect()->back();
    }
}
