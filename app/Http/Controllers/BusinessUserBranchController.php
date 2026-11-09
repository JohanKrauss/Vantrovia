<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessUserBranchController extends Controller
{
    public function edit(
        Business $business,
        User $user
    ) {
        $membership = $this->getMembership(
            $business,
            $user
        );

        $branches = $business
            ->branches()
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->get();

        $selectedBranchIds = $membership
            ->branches()
            ->pluck('branches.id')
            ->toArray();

        return view(
            'businesses.users.branches',
            compact(
                'business',
                'user',
                'membership',
                'branches',
                'selectedBranchIds'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        User $user
    ) {
        $membership = $this->getMembership(
            $business,
            $user
        );

        $data = $request->validate([
            'branches' => [
                'nullable',
                'array',
            ],

            'branches.*' => [
                'integer',

                Rule::exists('branches', 'id')
                    ->where(
                        fn($query) => $query->where(
                            'business_id',
                            $business->id
                        )
                    ),
            ],
        ]);

        $branchIds = $data['branches'] ?? [];

        $membership
            ->branches()
            ->sync($branchIds);

        return redirect()
            ->route(
                'businesses.users.branches.edit',
                [
                    $business,
                    $user,
                ]
            )
            ->with(
                'success',
                'Sucursales del usuario actualizadas correctamente.'
            );
    }

    private function getMembership(
        Business $business,
        User $user
    ): BusinessUser {
        return BusinessUser::query()
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'user_id',
                $user->id
            )
            ->firstOrFail();
    }
}
