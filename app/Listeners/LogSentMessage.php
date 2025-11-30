<?php

namespace App\Listeners;

use HMS\Entities\Email;
use HMS\Repositories\EmailRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\Mime\Part\AbstractMultipartPart;
use Symfony\Component\Mime\Part\TextPart;

class LogSentMessage
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
     * @param MessageSent $event
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $originalMessage = $event->sent->getOriginalMessage();
        if (! $originalMessage instanceof MimeEmail) {
            return;
        }

        // grab the various bits of info from the Symfony Sent Message
        $messageId = $event->sent->getMessageId();

        $toAddresses = collect($originalMessage->getTo())
            ->mapWithKeys(fn ($a, $k) => [$a->getAddress() => $a->getName() ?: null])
            ->toArray();
        $subject = $originalMessage->getSubject();

        $originalBody = $originalMessage->getBody();
        if (! $originalBody instanceof AbstractMultipartPart) {
            return;
        }
        $part = collect($originalBody->getParts())
            ->filter(fn ($tp) => $tp->getMediaSubtype() == 'html')
            ->first();
        if (! $part instanceof TextPart) {
            return;
        }
        $body = $part->getBody();

        $fullString = $originalMessage->toString();

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
