<?php

namespace App\Jobs;

use HMS\Entities\Email;
use HMS\Repositories\EmailRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogSentMailgunMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $to;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $messageId;

    /**
     * Create a new job instance.
     *
     * @param array $to
     * @param string $subject
     * @param string $body
     * @param string $messageId
     *
     * @return void
     */
    public function __construct(
        array $to,
        string $subject,
        string $body,
        string $messageId
    ) {
        //
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     *
     * @param EmailRepository $emailRepository
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        EmailRepository $emailRepository,
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $toAddresses = collect($this->to)->mapWithKeys(function ($name, $key) {
            return [$key => $name['name']];
        })->toArray();

        $fullString = '';

        $email = new Email($toAddresses, $this->subject, $this->body, $fullString, $this->messageId);

        // now work out the asscications for the users / roles
        foreach ($toAddresses as $address => $name) {
            if ($role = $roleRepository->findOneByEmail($address)) {
                $email->setRole($role);
            }
            if ($user = $userRepository->findOneByEmail($address)) {
                $email->getUsers()->add($user);
            }
        }

        $emailRepository->save($email);
    }
}
