<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    private function profile()
    {
        return Auth::user()->providerProfile;
    }

    public function index(): View
    {
        $profile      = $this->profile();
        $items        = ProviderGallery::where('provider_profile_id', $profile->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $user  = Auth::user();
        $plan  = $user->subscription_plan;
        $limit = $plan?->gallery_limit ?? 0;

        return view('provider.gallery.index', compact('items', 'limit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = $this->profile();
        if (!Auth::user()->canAddGalleryItem()) {
            return back()->withErrors(['image' => "Your plan limit has been reached. Please upgrade to add more items."]);
        }

        $request->validate([
            'image'      => ['required_without:video_url', 'nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp,mp4,mov', 'max:20480'],
            'video_url'  => ['required_without:image', 'nullable', 'string', 'url'],
            'caption'    => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $nextSort = $request->sort_order;
        if (is_null($nextSort)) {
            $max = ProviderGallery::where('provider_profile_id', $profile->id)->max('sort_order');
            $nextSort = ($max ?? -1) + 1;
        }

        if ($request->hasFile('image')) {
            $file    = $request->file('image');
            $isVideo = in_array($file->getMimeType(), ['video/mp4', 'video/quicktime']);
            $path    = $file->store('provider-gallery', 'public');
            $url     = $path;
        } else {
            $url     = $request->video_url;
            $isVideo = true;
        }

        ProviderGallery::create([
            'provider_profile_id' => $profile->id,
            'url'                 => $url,
            'caption'             => $request->caption,
            'sort_order'          => $nextSort,
            'is_video'            => $isVideo,
        ]);

        return back()->with('success', 'Item added to gallery.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate(['order' => ['required', 'array']]);
        $profileId = $this->profile()->id;

        foreach ($request->order as $index => $id) {
            ProviderGallery::where('id', $id)
                ->where('provider_profile_id', $profileId)
                ->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Gallery order updated.');
    }

    public function destroy(ProviderGallery $gallery): RedirectResponse
    {
        if ($gallery->provider_profile_id !== $this->profile()->id) {
            abort(403);
        }

        Storage::disk('public')->delete($gallery->url);
        $gallery->delete();

        return back()->with('success', 'Gallery item removed.');
    }
}
