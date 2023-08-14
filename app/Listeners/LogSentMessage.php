<?php

namespace App\Listeners;

use HMS\Entities\Email;
use HMS\Repositories\EmailRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;

class LogSentMessage implements ShouldQueue
{
    /**
     * @var EmailRepository
     */
    protected $emailRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @param EmailRepository $emailRepository
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function __construct(
        EmailRepository $emailRepository,
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $this->emailRepository = $emailRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param MessageSending $event
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        // grab the various bits of info from the Symfony Sent Message
        $messageId = $event->sent->getMessageId();
        $toAddresses = collect($event->sent->getOriginalMessage()->getTo())
            ->mapWithKeys(fn ($a, $k) => [$a->getAddress() => $a->getName() ?: null])
            ->toArray();
        $subject = $event->sent->getOriginalMessage()->getSubject();
        $body = collect(
            $event->sent->getOriginalMessage()->getBody()->getParts()
        )
            ->filter(fn ($tp) => $tp->getMediaSubtype() == 'html')
            ->first()
            ->getBody();
        $fullString = $event->sent->getOriginalMessage()->toString();

        $email = new Email($toAddresses, $subject, $body, $fullString, $messageId);

        // now work out the asscications for the users / roles
        foreach ($toAddresses as $address => $name) {
            if ($role = $this->roleRepository->findOneByEmail($address)) {
                $email->setRole($role);
            }
            if ($user = $this->userRepository->findOneByEmail($address)) {
                $email->getUsers()->add($user);
            }
        }

        $this->emailRepository->save($email);
    }
}
