<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(Request $request): View
    {
        return view('invitations.create', [
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isSuperAdmin() || $user->isAdmin(), 403);

        $roles = $user->isSuperAdmin() ? [User::ROLE_ADMIN] : [User::ROLE_ADMIN, User::ROLE_MEMBER];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($roles)],
        ];

        if ($user->isSuperAdmin()) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $company = $user->isSuperAdmin()
            ? Company::create(['name' => $validated['company_name']])
            : $user->company;

        abort_if($company === null, 403);

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => Str::random(48),
            'invited_by' => $user->id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Invitation created.')
            ->with('invitation_url', route('invitations.accept.show', $invitation->token));
    }

    public function showAccept(string $token): View
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        return view('invitations.accept', [
            'invitation' => $invitation,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'company_id' => $invitation->company_id,
            'name' => $invitation->name,
            'email' => $invitation->email,
            'password' => $validated['password'],
            'role' => $invitation->role,
            'email_verified_at' => now(),
        ]);
        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Invitation accepted.');
    }
}
