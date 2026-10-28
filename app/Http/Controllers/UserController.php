<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->withCount([
                'businesses as active_businesses_count' => function ($query) {
                    $query->where('business_user.is_active', true);
                },
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->status === 'active',
                fn($query) => $query->where('is_active', true)
            )
            ->when(
                $request->status === 'inactive',
                fn($query) => $query->where('is_active', false)
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'password' => Hash::make(Str::random(64)),
            'is_active' => $request->boolean('is_active'),
        ]);

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()
                ->route('users.index')
                ->with(
                    'success',
                    'Usuario creado correctamente. Se envió un correo para establecer su contraseña.'
                );
        }

        return redirect()
            ->route('users.index')
            ->with(
                'warning',
                'El usuario fue creado, pero no fue posible enviar el correo de contraseña.'
            );
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (
            auth()->id() === $user->id &&
            ! $request->boolean('is_active')
        ) {
            return back()
                ->withErrors([
                    'is_active' => 'No puedes desactivar tu propia cuenta.',
                ])
                ->withInput();
        }

        $user->update([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with(
                'warning',
                'No puedes desactivar tu propia cuenta.'
            );
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return back()->with(
            'success',
            $user->is_active
                ? 'Usuario activado correctamente.'
                : 'Usuario desactivado correctamente.'
        );
    }

    public function sendPasswordReset(User $user)
    {
        if (! $user->is_active) {
            return back()->with(
                'warning',
                'No se puede enviar recuperación a un usuario desactivado.'
            );
        }

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'Se envió el enlace para restablecer la contraseña.'
            );
        }

        return back()->with(
            'warning',
            'No fue posible enviar el correo de recuperación.'
        );
    }
}
