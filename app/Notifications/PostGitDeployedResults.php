<?php

namespace App\Notifications;

use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PostGitDeployedResults extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var bool
     */
    protected $success;

    /**
     * @var array
     */
    protected $commandResults;

    /**
     * @var Carbon
     */
    protected $startTime;

    /**
     * @var Carbon
     */
    protected $stopTime;

    /**
     * Create a new notification instance.
     *
     * @param bool $success
     * @param array $commandResults
     * @param Carbon $startTime
     * @param Carbon $stopTime
     */
    public function __construct(bool $success, array $commandResults, Carbon $startTime, Carbon $stopTime)
    {
        $this->success = $success;
        $this->commandResults = $commandResults;
        $this->startTime = $startTime;
        $this->stopTime = $stopTime;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('HMS Deploy results for ' . gethostname())
            ->markdown(
                'emails.postGitDeployed',
                [
                'teamName' => ($notifiable instanceof Role) ? $notifiable->getDisplayName() : $notifiable->getName(),
                'success' => $this->success,
                'commandResults' => $this->commandResults,
                'startTime' => $this->startTime,
                'stopTime' => $this->stopTime,
                'runTime' => $this->startTime->diffInMinutes($this->stopTime),
                ]
            );
    }
}
