<?php

namespace HMS\Composers;

use Illuminate\View\View;
use HMS\Governance\VotingManager;
use HMS\Repositories\Governance\ProxyRepository;
use HMS\Repositories\Governance\MeetingRepository;

class ProxyComposer
{
    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     * @var ProxyRepository
     */
    protected $proxyRepository;

    /**
     * @var VotingMnager
     */
    protected $votingManager;

    /**
     * Create a new profile composer.
     *
     * @param MeetingRepository $meetingRepository
     * @param ProxyRepository $proxyRepository
     * @param VotingMangaer $votingManager
     *
     * @return void
     */
    public function __construct(
        MeetingRepository $meetingRepository,
        ProxyRepository $proxyRepository,
        VotingManager $votingManager
    ) {
        $this->meetingRepository = $meetingRepository;
        $this->proxyRepository = $proxyRepository;
        $this->votingManager = $votingManager;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = \Auth::user();
        $votingStatus = $this->votingManager->getVotingStatusForUser($user);
        $meeting = $this->meetingRepository->findNext();
        $principal = $this->proxyRepository->findOneByPrincipal($meeting, $user);
        $proxies = $this->proxyRepository->findByProxy($meeting, $user);

        $view->with('user', $user)
            ->with('votingStatus', $votingStatus)
            ->with('meeting', $meeting)
            ->with('principal', $principal)
            ->with('proxies', $proxies);
    }
}
