<?php

namespace App\Notifications\ServiceRequest;

use App\Models\ServiceRequest;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestCancelled extends BaseNotification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public ?string $reason = null,
    ) {}

    public function getEventType(): string
    {
        return 'service_request.cancelled';
    }

    public function toDatabase(mixed $notifiable): array
    {
        $body = "Request #{$this->serviceRequest->request_number} has been cancelled.";
        if ($this->reason) {
            $body .= " Reason: {$this->reason}";
        }

        return [
            'type'  => 'service_request.cancelled',
            'title' => 'Service Request Cancelled',
            'body'  => $body,
            'url'   => $this->resolveUrl($notifiable),
            'meta'  => [
                'service_request_id' => $this->serviceRequest->id,
                'reason'             => $this->reason,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Request #{$this->serviceRequest->request_number} Cancelled — " . config('app.name'))
            ->view('emails.notifications.service-request', [
                'user'           => $notifiable,
                'serviceRequest' => $this->serviceRequest,
                'heading'        => 'Request Cancelled',
                'message'        => "Request #{$this->serviceRequest->request_number} has been cancelled." . ($this->reason ? " Reason: {$this->reason}" : ''),
                'actionUrl'      => $this->resolveUrl($notifiable),
                'actionText'     => 'View Details',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Request #{$this->serviceRequest->request_number} was cancelled." . ($this->reason ? " Reason: {$this->reason}" : '');
    }

    private function resolveUrl(mixed $notifiable): string
    {
        if ($notifiable->hasRole(['freelancer', 'business'])) {
            return route('provider.requests.show', $this->serviceRequest);
        }
        return route('customer.requests.show', $this->serviceRequest);
    }
}
