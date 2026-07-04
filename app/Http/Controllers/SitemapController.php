<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProviderProfile;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = view('sitemap.index')->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function pages(): Response
    {
        $content = view('sitemap.pages')->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function providers(): Response
    {
        $providers = ProviderProfile::where('status', 'active')
            ->where('verification_status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->select(['slug', 'updated_at'])
            ->get();

        $content = view('sitemap.providers', compact('providers'))->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function categories(): Response
    {
        $categories = Category::where('status', 'active')
            ->orderBy('sort_order')
            ->select(['id', 'slug', 'updated_at'])
            ->get();

        $content = view('sitemap.categories', compact('categories'))->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
