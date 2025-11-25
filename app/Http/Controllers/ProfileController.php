<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user()->load('residentProfile')
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'purok' => ['nullable', 'string', 'max:50'],
        ]);

        $request->user()->update($validated);

        if ($request->user()->residentProfile) {
            $request->user()->residentProfile->update([
                'address_line' => $validated['address_line'] ?? null,
                'purok' => $validated['purok'] ?? null,
                'contact_number' => $validated['phone'] ?? null,
            ]);
        }

        return back()->with('status', 'Profile updated.');
    }
}
