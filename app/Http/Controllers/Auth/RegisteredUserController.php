<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                // Hash password before saving to prevent plain-text storage
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            // Do not automatically log the user in. Redirect to welcome
            // so they can click "Log in" and enter their credentials.
            session()->flash('success', 'Registration successful! Please log in to continue.');

            return redirect(route('welcome.page', absolute: false));
    }
}
