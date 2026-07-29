<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:60'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'bio' => ['nullable', 'string', 'max:280'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'username.regex' => 'The username may only contain lowercase letters, numbers and underscores.',
        ]);

        $user = User::create($validated);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('feed'))
            ->with('status', "Welcome aboard, {$user->displayName()} 👋");
    }
}
