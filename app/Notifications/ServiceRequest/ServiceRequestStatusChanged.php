<?php

namespace App\Notifications\ServiceRequest;

use App\Models\ServiceRequest;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestStatusChanged extends BaseNotification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function getEventType(): string
    {
        return 'service_request.status_changed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'service_request.status_changed',
            'title' => 'Service Request Status Updated',
            'body'  => "Your request #{$this->serviceRequest->request_number} status changed from {$this->oldStatus} to {$this->newStatus}.",
            'url'   => $this->resolveUrl($notifiable),
            'meta'  => [
                'service_request_id' => $this->serviceRequest->id,
                'old_status'         => $this->oldStatus,
                'new_status'         => $this->newStatus,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Request #{$this->serviceRequest->request_number} — Status Updated — " . config('app.name'))
            ->view('emails.notifications.service-request', [
                'user'           => $notifiable,
                'serviceRequest' => $this->serviceRequest,
                'heading'        => 'Request Status Updated',
                'message'        => "Request #{$this->serviceRequest->request_number} is now <strong>{$this->newStatus}</strong>.",
                'actionUrl'      => $this->resolveUrl($notifiable),
                'actionText'     => 'View Request',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Request #{$this->serviceRequest->request_number} is now {$this->newStatus}. " . $this->resolveUrl($notifiable);
    }

    private function resolveUrl(mixed $notifiable): string
    {
        // Customer or Provider — link to the appropriate panel
        if ($notifiable->hasRole(['freelancer', 'business'])) {
            return route('provider.requests.show', $this->serviceRequest);
        }
        return route('customer.requests.show', $this->serviceRequest);
    }
}
