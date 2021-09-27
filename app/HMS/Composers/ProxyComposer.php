<?php

namespace HMS\Composers;

use HMS\Repositories\Governance\MeetingRepository;
use HMS\Repositories\Governance\ProxyRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
     * Create a new profile composer.
     *
     * @param MeetingRepository $meetingRepository
     * @param ProxyRepository $proxyRepository
     *
     * @return void
     */
    public function __construct(
        MeetingRepository $meetingRepository,
        ProxyRepository $proxyRepository
    ) {
        $this->meetingRepository = $meetingRepository;
        $this->proxyRepository = $proxyRepository;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        $meeting = $this->meetingRepository->findNext();
        if (is_null($meeting)) {
            return;
        }
        $principal = $this->proxyRepository->findOneByPrincipal($meeting, $user);
        $proxies = $this->proxyRepository->findByProxy($meeting, $user);

        $view->with('meeting', $meeting)
            ->with('principal', $principal)
            ->with('proxies', $proxies);
    }
}
