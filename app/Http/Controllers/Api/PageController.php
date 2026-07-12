<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;

class PageController extends Controller
{
    private function getPageResponse($key, $defaultContent, $title)
    {
        $content = Setting::get($key, $defaultContent);
        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    public function aboutUs()
    {
        return $this->getPageResponse('about_us', ['title' => 'About PickHandy'], 'About Us');
    }

    public function safetyCenter()
    {
        return $this->getPageResponse('safety_center', ['title' => 'Safety Center'], 'Safety Center');
    }

    public function howItWorks()
    {
        return $this->getPageResponse('how_it_works', ['title' => 'How it Works'], 'How it Works');
    }

    public function privacyPolicy()
    {
        return $this->getPageResponse('privacy_policy', ['title' => 'Privacy Policy'], 'Privacy Policy');
    }

    public function termsConditions()
    {
        return $this->getPageResponse('terms_conditions', ['title' => 'Terms & Conditions'], 'Terms & Conditions');
    }
}
