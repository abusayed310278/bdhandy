<?php

namespace App\Notifications\TeamJob;

use App\Models\TeamJobAssignment;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TeamJobStatusChanged extends BaseNotification
{
    public function __construct(
        public TeamJobAssignment $assignment,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function getEventType(): string
    {
        return 'team_job.status_changed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        $url = route('tech.schedule.today');
        if ($notifiable instanceof \App\Models\User) {
            if ($notifiable->isTeamMember()) {
                $url = route('tech.jobs.show', $this->assignment->id);
            } elseif ($notifiable->isProvider()) {
                $url = route('provider.requests.show', $this->assignment->service_request_id);
            }
        }

        return [
            'type'  => 'team_job.status_changed',
            'title' => 'Job Status Updated',
            'body'  => "Job assignment status changed from {$this->oldStatus} to {$this->newStatus}.",
            'url'   => $url,
            'meta'  => [
                'assignment_id' => $this->assignment->id,
                'old_status'    => $this->oldStatus,
                'new_status'    => $this->newStatus,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = route('tech.schedule.today');
        if ($notifiable instanceof \App\Models\User) {
            if ($notifiable->isTeamMember()) {
                $url = route('tech.jobs.show', $this->assignment->id);
            } elseif ($notifiable->isProvider()) {
                $url = route('provider.requests.show', $this->assignment->service_request_id);
            }
        }

        return (new MailMessage)
            ->subject("Job Status Updated — " . config('app.name'))
            ->view('emails.notifications.team-job', [
                'user'       => $notifiable,
                'assignment' => $this->assignment,
                'heading'    => 'Job Status Changed',
                'message'    => "A job assignment status has changed from <strong>{$this->oldStatus}</strong> to <strong>{$this->newStatus}</strong>.",
                'actionUrl'  => $url,
                'actionText' => 'View Details',
            ]);
    }
}
