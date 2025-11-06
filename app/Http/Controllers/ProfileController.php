<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('profile.index', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $validated['updated_by'] = Auth::id();
        
        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($validated['password']),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Remove the user's avatar.
     */
    public function destroyAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->update([
            'avatar' => null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Avatar removed successfully!');
    }

    /**
     * Display user's activity log.
     */
    public function activity()
    {
        $user = Auth::user();
        
        // You can implement activity log later
        $activities = [];
        
        return view('profile.activity', compact('user', 'activities'));
    }

    /**
     * Show account settings.
     */
    public function settings()
    {
        $user = Auth::user();
        
        return view('profile.settings', compact('user'));
    }

    /**
     * Update account settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'is_active' => ['boolean'],
            // Add more settings fields as needed
        ]);

        $user = Auth::user();
        $validated['updated_by'] = Auth::id();
        
        $user->update($validated);

        return redirect()->route('profile.settings')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Deactivate user account.
     */
    public function deactivate(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        
        $user->update([
            'is_active' => false,
            'updated_by' => Auth::id(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Your account has been deactivated.');
    }

    /**
     * Delete user account permanently.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        
        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        
        $user->delete(); // Soft delete

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Your account has been deleted.');
    }
}