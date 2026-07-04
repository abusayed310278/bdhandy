@if (session()->has('notify'))
    @php
        $notificationModel = session()->get('notify.model');
    @endphp

    @if ($notificationModel && is_object($notificationModel) && method_exists($notificationModel, 'getViewName'))
        @include($notificationModel->getViewName())
    @endif

    <script>
        const notification = document.querySelector('div.notify');
        const notify = {
            timeout: {{ session()->get('notify.duration', config('notify.timeout', 5000)) }},
        }

        if (notification) {
            setTimeout(() => {
                notification.remove();
            }, notify.timeout);
        }
    </script>
@endif
