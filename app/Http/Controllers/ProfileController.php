<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $orders = $user->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('profile.edit', compact('user', 'orders'));
    }

    public function update(Request $request, UpdateUserProfileInformation $updateUserProfileInformation): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $updateUserProfileInformation->update($user, $request->only(['name', 'email']));

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Tu perfil se actualizó correctamente.');
    }
}
