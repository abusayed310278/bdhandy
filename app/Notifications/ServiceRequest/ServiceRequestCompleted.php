<?php

namespace App\Notifications\ServiceRequest;

use App\Models\ServiceRequest;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestCompleted extends BaseNotification
{
    public function __construct(public ServiceRequest $serviceRequest) {}

    public function getEventType(): string
    {
        return 'service_request.completed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'service_request.completed',
            'title' => 'Service Request Completed',
            'body'  => "Request #{$this->serviceRequest->request_number} has been marked as completed.",
            'url'   => $this->resolveUrl($notifiable),
            'meta'  => ['service_request_id' => $this->serviceRequest->id],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Request #{$this->serviceRequest->request_number} Completed — " . config('app.name'))
            ->view('emails.notifications.service-request', [
                'user'           => $notifiable,
                'serviceRequest' => $this->serviceRequest,
                'heading'        => 'Service Completed!',
                'message'        => "Request #{$this->serviceRequest->request_number} has been completed. Thank you for using " . config('app.name') . ".",
                'actionUrl'      => $this->resolveUrl($notifiable),
                'actionText'     => 'View Details',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Request #{$this->serviceRequest->request_number} is complete. Leave a review: " . $this->resolveUrl($notifiable);
    }

    private function resolveUrl(mixed $notifiable): string
    {
        if ($notifiable->hasRole(['freelancer', 'business'])) {
            return route('provider.requests.show', $this->serviceRequest);
        }
        return route('customer.requests.show', $this->serviceRequest);
    }
}
