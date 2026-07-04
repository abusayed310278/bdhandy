<?php

namespace App\Notifications\TeamJob;

use App\Models\TeamJobAssignment;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TeamJobAssigned extends BaseNotification
{
    public function __construct(public TeamJobAssignment $assignment) {}

    public function getEventType(): string
    {
        return 'team_job.assigned';
    }

    public function toDatabase(mixed $notifiable): array
    {
        $request = $this->assignment->request;
        $scheduledAt = $this->assignment->scheduled_start_time?->format('D, d M Y H:i');

        $url = route('tech.schedule.today');
        if ($notifiable instanceof \App\Models\User) {
            if ($notifiable->isTeamMember()) {
                $url = route('tech.jobs.show', $this->assignment->id);
            } elseif ($notifiable->isProvider()) {
                $url = route('provider.requests.show', $this->assignment->service_request_id);
            }
        }

        return [
            'type'  => 'team_job.assigned',
            'title' => 'New Job Assigned to You',
            'body'  => "You have been assigned a new job" . ($scheduledAt ? " scheduled for {$scheduledAt}" : '') . ".",
            'url'   => $url,
            'meta'  => [
                'assignment_id'      => $this->assignment->id,
                'service_request_id' => $this->assignment->service_request_id,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $scheduledAt = $this->assignment->scheduled_start_time?->format('D, d M Y H:i');

        $url = route('tech.schedule.today');
        if ($notifiable instanceof \App\Models\User) {
            if ($notifiable->isTeamMember()) {
                $url = route('tech.jobs.show', $this->assignment->id);
            } elseif ($notifiable->isProvider()) {
                $url = route('provider.requests.show', $this->assignment->service_request_id);
            }
        }

        return (new MailMessage)
            ->subject("New Job Assignment — " . config('app.name'))
            ->view('emails.notifications.team-job', [
                'user'       => $notifiable,
                'assignment' => $this->assignment,
                'heading'    => 'New Job Assigned',
                'message'    => "You have been assigned a new job" . ($scheduledAt ? " scheduled for {$scheduledAt}" : '') . ". Please review the details.",
                'actionUrl'  => $url,
                'actionText' => 'View Details',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        $scheduledAt = $this->assignment->scheduled_start_time?->format('d M H:i');
        return config('app.name') . ": New job assigned to you" . ($scheduledAt ? " on {$scheduledAt}" : '') . ". Check your schedule.";
    }
}
