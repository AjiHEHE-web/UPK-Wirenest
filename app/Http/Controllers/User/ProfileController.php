<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('pages.user.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $attributes = $request->safe()->except('profil_user');
        $attributes['nama'] = $attributes['name'];
        $image = $request->file('profil_user');
        $oldImage = $user->profil_user;

        if ($image !== null) {
            $path = $image->store('profiles', 'public');

            if ($path === false) {
                throw new RuntimeException('Foto profil gagal disimpan.');
            }

            $attributes['profil_user'] = $path;
        }

        try {
            $user->fill($attributes);
            $user->save();
        } catch (\Throwable $exception) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        if (isset($path) && $oldImage !== null) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('user.profile.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return redirect()->route('user.profile.show')->with('success', 'Password berhasil diperbarui.');
    }
}
