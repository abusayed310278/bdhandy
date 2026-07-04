<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Currency;
use App\Models\Message;
use App\Models\ProviderProfile;
use App\Models\RequestAttachment;
use App\Models\RequestStatusLog;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PublicRequestController extends Controller
{
    /**
     * Store a new service request from the provider profile page.
     */
    public function store(Request $request, ProviderProfile $provider)
    {
        $user = Auth::user();
        $isComplete = false;
        if ($user->isCustomer()) {
            $isComplete = $user->onboarding_profile_done && $user->customerAddresses()->exists();
        } elseif ($user->isProvider()) {
            $isComplete = $user->providerProfile && $user->providerProfile->verification_status === 'approved';
        }

        if (!$isComplete) {
            $redirectUrl = route('dashboard');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'profile_incomplete', 'redirect' => $redirectUrl], 403);
            }
            return redirect($redirectUrl)
                ->with('warning', 'Please complete your profile before submitting a request.');
        }

        $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string', 'max:2000'],
            'preferred_date'      => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time'      => ['nullable', 'string', 'max:10'],
            'urgency'             => ['required', 'in:normal,urgent,emergency'],
            'provider_service_id' => ['nullable', 'exists:provider_services,id'],
            'address'             => ['nullable', 'string', 'max:500'],
            'attachments.*'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,mp4', 'max:10240'],
        ]);

        // Generate human-readable request number
        $number = 'REQ-' . now()->format('Y') . '-' . str_pad(
            ServiceRequest::withTrashed()->count() + 1,
            6, '0', STR_PAD_LEFT
        );

        // Use a default currency (first active)
        $currency = Currency::where('status', 'active')->first();

        // Resolve service_id from the chosen provider service, or fall back to first active service
        $providerServiceId = $request->provider_service_id ?: null;
        $providerService = $providerServiceId
            ? $provider->services()->where('id', $providerServiceId)->where('status', 'active')->first()
            : ($provider->services()->where('status', 'active')->first() ?? $provider->services()->first());

        $serviceId = $providerService?->service_id;
        if (!$serviceId) {
            $serviceId = \App\Models\Service::where('status', 'active')->value('id')
                ?? \App\Models\Service::value('id');
        }

        $serviceRequest = ServiceRequest::create([
            'customer_id'         => Auth::id(),
            'provider_id'         => $provider->user_id,
            'service_id'          => $serviceId,
            'provider_service_id' => $providerService?->id,
            'request_number'      => $number,
            'title'               => $request->title,
            'description'         => $request->description,
            'preferred_date'      => $request->preferred_date,
            'preferred_time'      => $request->preferred_time,
            'urgency'             => $request->urgency,
            'address'             => $request->address,
            'currency_id'         => $currency?->id,
            'request_status'      => 'pending',
            'payment_status'      => 'pending',
        ]);

        RequestStatusLog::create([
            'service_request_id' => $serviceRequest->id,
            'old_status'         => null,
            'new_status'         => 'pending',
            'changed_by'         => Auth::id(),
            'notes'              => 'Request submitted by customer',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('request-attachments', 'public');
                RequestAttachment::create([
                    'service_request_id' => $serviceRequest->id,
                    'file'               => $path,
                    'file_type'          => str_starts_with($file->getMimeType(), 'image/') ? 'image' : (str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'pdf'),
                ]);
            }
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['ok' => true, 'request_number' => $number]);
        }

        return redirect()->route('customer.requests.show', $serviceRequest)
            ->with('success', "Request {$number} submitted! The provider will respond shortly.");
    }

    /**
     * Send a direct message to a provider.
     */
    public function sendMessage(Request $request, ProviderProfile $provider)
    {
        $user = Auth::user();
        $isComplete = false;
        if ($user->isCustomer()) {
            $isComplete = $user->onboarding_profile_done && $user->customerAddresses()->exists();
        } elseif ($user->isProvider()) {
            $isComplete = $user->providerProfile && $user->providerProfile->verification_status === 'approved';
        }

        if (!$isComplete) {
            $redirectUrl = route('dashboard');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'profile_incomplete', 'redirect' => $redirectUrl], 403);
            }
            return redirect($redirectUrl)
                ->with('warning', 'Please complete your profile before sending messages.');
        }

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::user();

        // Find or create a unique conversation between the customer and provider
        $conversation = Conversation::firstOrCreate([
            'customer_id' => $user->id,
            'provider_id' => $provider->user_id,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message'         => $request->message,
            'message_type'    => 'text',
            'is_read'         => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'conversation_id' => $conversation->id]);
        }

        return redirect()->route('customer.conversations.show', $conversation)
            ->with('success', 'Message sent! The provider will reply soon.');
    }

    /**
     * Get conversation messages between the customer and provider.
     */
    public function getMessages(Request $request, ProviderProfile $provider)
    {
        if (!Auth::check()) {
            return response()->json(['messages' => [], 'logged_in' => false]);
        }

        $user = Auth::user();

        $conversation = Conversation::where('customer_id', $user->id)
            ->where('provider_id', $provider->user_id)
            ->first();

        if (!$conversation) {
            return response()->json([
                'messages' => [],
                'logged_in' => true,
                'conversation_id' => null
            ]);
        }

        $messagesQuery = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc');

        $perPage = 20;
        $messagesPaginated = $messagesQuery->paginate($perPage);

        // Reverse to maintain chronological order
        $messages = collect($messagesPaginated->items())->reverse()->values();

        return response()->json([
            'messages' => $messages,
            'has_more' => $messagesPaginated->hasMorePages(),
            'current_page' => $messagesPaginated->currentPage(),
            'conversation_id' => $conversation->id,
            'logged_in' => true,
            'current_user_id' => $user->id
        ]);
    }
}

