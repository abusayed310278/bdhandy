@include('emails.notifications.layout', [
    'heading'    => $heading,
    'message'    => $message,
    'actionUrl'  => $actionUrl ?? null,
    'actionText' => $actionText ?? null,
])
