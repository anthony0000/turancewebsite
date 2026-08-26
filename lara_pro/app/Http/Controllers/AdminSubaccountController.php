<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSubaccountController extends Controller
{
    public function index(): View
    {
        return view('admin.subaccounts.index', [
            'subaccounts' => User::query()
                ->where('role', AdminAccess::ROLE_SUBACCOUNT)
                ->latest('updated_at')
                ->get(),
            'permissionOptions' => AdminAccess::permissions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.subaccounts.form', [
            'user' => null,
            'permissionOptions' => AdminAccess::permissions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request, creating: true);

        User::query()->create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'job_title' => $this->nullableTrim($validated['job_title'] ?? null),
            'phone' => $this->nullableTrim($validated['phone'] ?? null),
            'password' => $validated['password'],
            'role' => AdminAccess::ROLE_SUBACCOUNT,
            'permissions' => array_values($validated['permissions'] ?? []),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.subaccounts.index')
            ->with('status', 'Staff account created with limited access.');
    }

    public function edit(User $user): View
    {
        $this->ensureSubaccount($user);

        return view('admin.subaccounts.form', [
            'user' => $user,
            'permissionOptions' => AdminAccess::permissions(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureSubaccount($user);
        $validated = $this->validateRequest($request, creating: false, user: $user);

        $user->fill([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'job_title' => $this->nullableTrim($validated['job_title'] ?? null),
            'phone' => $this->nullableTrim($validated['phone'] ?? null),
            'permissions' => array_values($validated['permissions'] ?? []),
        ]);

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.subaccounts.index')
            ->with('status', 'Staff account access updated.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $this->ensureSubaccount($user);
        $user->update(['is_active' => ! $user->is_active]);

        return redirect()
            ->route('admin.subaccounts.index')
            ->with('status', $user->is_active ? 'Staff account activated.' : 'Staff account suspended.');
    }

    /** @return array<string, mixed> */
    private function validateRequest(Request $request, bool $creating, ?User $user = null): array
    {
        $passwordRules = $creating
            ? ['required', 'string', 'min:8', 'max:72', 'confirmed']
            : ['nullable', 'string', 'min:8', 'max:72', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'password' => $passwordRules,
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminAccess::permissionKeys())],
        ]);
    }

    private function ensureSubaccount(User $user): void
    {
        abort_unless($user->role === AdminAccess::ROLE_SUBACCOUNT, 404);
    }

    private function nullableTrim(?string $value): ?string
    {
        return filled($value) ? trim($value) : null;
    }
}
