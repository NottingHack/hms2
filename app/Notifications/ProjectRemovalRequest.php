<?php

namespace App\Notifications;

use HMS\Entities\Members\Project;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class ProjectRemovalRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Project
     */
    protected $project;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
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
        $channels = ['mail'];

        return $channels;
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
        $roleRepository = App::make(RoleRepository::class);
        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);

        return (new MailMessage)
            ->subject(config('branding.space_name') . ': Torts (Interference with Goods) Act 1977 - Removal requested')
            ->from($trusteesTeamRole->getEmail())
            ->markdown(
                'emails.membership.projectRemoval',
                [
                    'fullname' => $this->project->getUser()->getFullname(),
                    'projectNumber' => $this->project->getId(),
                    'projectName' => $this->project->getProjectName(),
                    'removalReason' => $this->project->getTortReason(),
                ]
            );
    }
}
