<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->email)),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->is_active) {
            return back()
                ->withErrors([
                    'email' => 'Esta cuenta se encuentra desactivada.',
                ])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'El correo electrónico o la contraseña son incorrectos.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();


        if ($user->isPlatformAdmin()) {

            $request->session()->forget(
                'current_business_id'
            );

            return redirect()->route(
                'dashboard'
            );
        }


        $businesses = $user
            ->businesses()
            ->wherePivot('is_active', true)
            ->where('businesses.status', 'active')
            ->get();


        if ($businesses->count() === 1) {

            $business = $businesses->first();

            $request->session()->put(
                'current_business_id',
                $business->id
            );

            return redirect()->route(
                'business.dashboard',
                $business
            );
        }


        $request->session()->forget(
            'current_business_id'
        );

        return redirect()->route(
            'business.select'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
