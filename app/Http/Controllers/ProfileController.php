<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
        try {
            $user = $request->user();
            $data = $request->validated();

            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::delete('user_profile_images/' . $user->profile_image);
                }

                // Store new image
                $imageName = time() . '.' . $request->profile_image->extension();
                $request->profile_image->storeAs('user_profile_images', $imageName);
                $data['profile_image'] = 'storage/user_profile_images/' . $imageName;
            }

            $user->fill($data);

            $user->save();

            return redirect()->back()->with([
                'alert-type' => 'success',
                'alert-message' => 'Profile updated successfully!',
                'alert-icon' => 'bx bxs-check-circle'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'alert-type' => 'danger',
                'alert-message' => 'There was an error updating the profile: ' . $e->getMessage(),
                'alert-icon' => 'bx bxs-message-square-x'
            ]);
        }
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