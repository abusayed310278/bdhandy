<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:5120', // 5MB max
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('dob')) {
            $user->date_of_birth = $request->dob;
        }
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $user->photo))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->photo));
            }
            
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = '/storage/' . $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
