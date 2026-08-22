<?php

namespace App\Http\Controllers;

use App\Support\AdminAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.index', [
            'user' => $this->account(),
            'isFullAdmin' => AdminAccess::isFullAdmin(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->account();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'password' => ['nullable', 'string', 'min:8', 'max:72', 'confirmed'],
        ]);

        $user->fill([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'job_title' => $this->nullableTrim($validated['job_title'] ?? null),
            'phone' => $this->nullableTrim($validated['phone'] ?? null),
        ]);

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();
        AdminAccess::signIn($request, $user->refresh());

        return redirect()
            ->route('admin.profile')
            ->with('status', 'Admin profile settings saved.');
    }

    private function account(): \App\Models\User
    {
        $user = AdminAccess::currentUser();

        abort_unless($user && in_array($user->role, [AdminAccess::ROLE_ADMIN, AdminAccess::ROLE_SUBACCOUNT], true), 403);

        return $user;
    }

    private function nullableTrim(?string $value): ?string
    {
        return filled($value) ? trim($value) : null;
    }
}
