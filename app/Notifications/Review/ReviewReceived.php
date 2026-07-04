<?php

namespace App\Notifications\Review;

use App\Models\Review;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewReceived extends BaseNotification
{
    public function __construct(public Review $review) {}

    public function getEventType(): string
    {
        return 'review.received';
    }

    public function toDatabase(mixed $notifiable): array
    {
        $stars = str_repeat('★', $this->review->rating) . str_repeat('☆', 5 - $this->review->rating);

        return [
            'type'  => 'review.received',
            'title' => 'You Received a New Review',
            'body'  => "{$this->review->customer?->name} left you a {$this->review->rating}-star review {$stars}.",
            'url'   => route('provider.reviews.index'),
            'meta'  => [
                'review_id' => $this->review->id,
                'rating'    => $this->review->rating,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Review — " . config('app.name'))
            ->view('emails.notifications.review', [
                'user'       => $notifiable,
                'review'     => $this->review,
                'heading'    => 'You Have a New Review',
                'message'    => "{$this->review->customer?->name} rated you {$this->review->rating}/5 stars: \"{$this->review->review}\"",
                'actionUrl'  => route('provider.reviews.index'),
                'actionText' => 'View Review',
            ]);
    }
}
