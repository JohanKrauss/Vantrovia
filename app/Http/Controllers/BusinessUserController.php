<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;

class BusinessUserController extends Controller
{
    public function index(Business $business)
    {
        $business->load([
            'users' => function ($query) {
                $query->orderBy('name');
            },
        ]);

        $availableUsers = User::query()
            ->whereDoesntHave(
                'businesses',
                function ($query) use ($business) {
                    $query->where(
                        'businesses.id',
                        $business->id
                    );
                }
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
                'is_active',
            ]);

        return view(
            'businesses.users.index',
            compact(
                'business',
                'availableUsers'
            )
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $data = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],
        ]);

        $user = User::findOrFail(
            $data['user_id']
        );

        $alreadyAssigned = $business
            ->users()
            ->where(
                'users.id',
                $user->id
            )
            ->exists();

        if ($alreadyAssigned) {
            return back()->with(
                'warning',
                'El usuario ya pertenece a este negocio.'
            );
        }

        $business->users()->attach(
            $user->id,
            [
                'is_active' => true,
            ]
        );

        return back()->with(
            'success',
            'Usuario asignado al negocio correctamente.'
        );
    }

    public function updateStatus(
        Request $request,
        Business $business,
        User $user
    ) {
        $data = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $membershipExists = $business
            ->users()
            ->where(
                'users.id',
                $user->id
            )
            ->exists();

        if (! $membershipExists) {
            return back()->with(
                'warning',
                'El usuario no pertenece a este negocio.'
            );
        }

        $business->users()->updateExistingPivot(
            $user->id,
            [
                'is_active' => $data['is_active'],
            ]
        );

        return back()->with(
            'success',
            $data['is_active']
                ? 'Usuario activado en el negocio.'
                : 'Usuario desactivado del negocio.'
        );
    }
}
