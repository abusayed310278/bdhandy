<?php

namespace App\Notifications\TeamJob;

use App\Models\TeamJobAssignment;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TeamJobCompleted extends BaseNotification
{
    public function __construct(public TeamJobAssignment $assignment) {}

    public function getEventType(): string
    {
        return 'team_job.completed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'team_job.completed',
            'title' => 'Job Completed',
            'body'  => "A team member has completed their assigned job for request #{$this->assignment->request?->request_number}.",
            'url'   => route('provider.requests.show', $this->assignment->service_request_id),
            'meta'  => [
                'assignment_id'      => $this->assignment->id,
                'service_request_id' => $this->assignment->service_request_id,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Job Completed — " . config('app.name'))
            ->view('emails.notifications.team-job', [
                'user'       => $notifiable,
                'assignment' => $this->assignment,
                'heading'    => 'Job Completed',
                'message'    => "{$this->assignment->member?->user?->name} has completed the job for request #{$this->assignment->request?->request_number}.",
                'actionUrl'  => route('provider.requests.show', $this->assignment->service_request_id),
                'actionText' => 'View Request',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Job for request #{$this->assignment->request?->request_number} completed by {$this->assignment->member?->user?->name}.";
    }
}
