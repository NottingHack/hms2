<?php

namespace App\Listeners;

use HMS\Entities\Email;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\EmailRepository;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @return void
     */
    public function __construct(EmailRepository $emailRepository, UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->emailRepository = $emailRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  IlluminateMailEventsMessageSending  $event
     * @return void
     */
    public function handle(MessageSending $event)
    {
        // grab the various bits of info from the Swift_Message
        $toAddresses = $event->message->getHeaders()->get('to')->getNameAddresses();
        $subject = $event->message->getHeaders()->get('subject')->getValue();
        $body = $event->message->getBody();

        $email = new Email($toAddresses, $subject, $body);

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
