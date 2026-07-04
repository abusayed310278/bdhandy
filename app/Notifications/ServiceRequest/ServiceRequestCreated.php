<?php

namespace App\Notifications\ServiceRequest;

use App\Models\ServiceRequest;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestCreated extends BaseNotification
{
    public function __construct(public ServiceRequest $serviceRequest) {}

    public function getEventType(): string
    {
        return 'service_request.submitted';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'service_request.submitted',
            'title' => 'New Service Request Received',
            'body'  => "You have a new service request #{$this->serviceRequest->request_number}: {$this->serviceRequest->title}.",
            'url'   => route('provider.requests.show', $this->serviceRequest),
            'meta'  => ['service_request_id' => $this->serviceRequest->id],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Service Request #{$this->serviceRequest->request_number} — " . config('app.name'))
            ->view('emails.notifications.service-request', [
                'user'           => $notifiable,
                'serviceRequest' => $this->serviceRequest,
                'heading'        => 'New Service Request',
                'message'        => "You have received a new service request from {$this->serviceRequest->customer?->name}.",
                'actionUrl'      => route('provider.requests.show', $this->serviceRequest),
                'actionText'     => 'View Request',
            ]);
    }
}
