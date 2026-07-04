<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProviderProfileController extends Controller
{
    public function edit(): View
    {
        $profile   = Auth::user()->providerProfile;
        $languages = Language::getActiveLanguages();
        $currencies = Currency::where('status', 'active')->get();
        $taglineSuggestions = $this->loadTaglineSuggestions();

        return view('provider.profile.edit', compact('profile', 'languages', 'currencies', 'taglineSuggestions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        // Prepend https:// to website and social links if not present
        foreach (['website', 'facebook_url', 'instagram_url', 'youtube_url'] as $key) {
            if ($request->filled($key)) {
                $val = trim($request->input($key));
                if (!preg_match('/^(https?:\/\/)/i', $val)) {
                    $request->merge([$key => 'https://' . $val]);
                }
            }
        }

        $request->validate([
            'business_name'       => ['required', 'string', 'max:255'],
            'tagline'             => ['nullable', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:2000'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'experience_level'    => ['required', 'in:beginner,intermediate,expert'],
            'currency_id'         => ['required', 'exists:currencies,id'],
            'primary_phone'       => ['required', 'string', 'max:30'],
            'whatsapp_number'     => ['nullable', 'string', 'max:30'],
            'languages'           => ['required', 'array', 'min:1'],
            'emergency_available' => ['nullable', 'boolean'],
            'website'             => ['nullable', 'url', 'max:255'],
            'facebook_url'        => ['nullable', 'url', 'max:255'],
            'instagram_url'       => ['nullable', 'url', 'max:255'],
            'youtube_url'         => ['nullable', 'url', 'max:255'],
            'logo'                => ['nullable', 'image', 'max:2048'],
            'cover_photo'         => ['nullable', 'image', 'max:4096'],
        ]);

        $data = [
            'business_name'       => $request->business_name,
            'tagline'             => $request->tagline,
            'description'         => $request->description,
            'years_of_experience' => $request->years_of_experience,
            'experience_level'    => $request->experience_level,
            'currency_id'         => $request->currency_id,
            'primary_phone'       => $request->primary_phone,
            'whatsapp_number'     => $request->whatsapp_number,
            'languages'           => $request->input('languages') ?? [],
            'emergency_available' => $request->boolean('emergency_available'),
            'website'             => $request->website,
            'facebook_url'        => $request->facebook_url,
            'instagram_url'       => $request->instagram_url,
            'youtube_url'         => $request->youtube_url,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('provider-logos', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('provider-covers', 'public');
        }

        $profile->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    private function loadTaglineSuggestions(): array
    {
        $path = public_path('tagline.txt');
        if (!file_exists($path)) {
            return [];
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_values(array_filter(array_map('trim', $lines)));
    }
}
