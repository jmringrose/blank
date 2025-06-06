<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function theme(Request $request)
    {
        /* save a string describing the user's theme preferecnce */
        // Validate the request
        $validated = $request->validate([
            'id' => 'required',
            'theme' => 'required|string',
        ]);

        // Find the user and update their theme preference
        $user = User::find($request->id);

        if ($user) {
            $user->theme = $request->theme;
            $user->save();

            // Return a JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Theme updated successfully'
            ]);
        }

        // Return error if user not found
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);

    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
