<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchContextController extends Controller
{
    public function index(
        Request $request,
        Business $business
    ) {
        abort_unless(
            $business->status === 'active',
            403
        );

        $user = $request->user();

        if ($user->isPlatformAdmin()) {
            $branches = $business
                ->branches()
                ->where('status', 'active')
                ->orderByDesc('is_main')
                ->orderBy('name')
                ->get();
        } else {
            $membership = BusinessUser::query()
                ->where('business_id', $business->id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->firstOrFail();

            $branches = $membership
                ->branches()
                ->where(
                    'branches.business_id',
                    $business->id
                )
                ->where(
                    'branches.status',
                    'active'
                )
                ->orderByDesc(
                    'branches.is_main'
                )
                ->orderBy(
                    'branches.name'
                )
                ->get();
        }

        /*
         * Si solamente tiene una sucursal disponible,
         * no tiene sentido mostrar el selector.
         */
        if ($branches->count() === 1) {
            $branch = $branches->first();

            $request->session()->put(
                'current_business_id',
                $business->id
            );

            $request->session()->put(
                'current_branch_id',
                $branch->id
            );

            return redirect()->route(
                'business.dashboard',
                $business
            );
        }

        return view(
            'business.branch-select',
            compact(
                'business',
                'branches'
            )
        );
    }

    public function switch(
        Request $request,
        Business $business
    ) {
        abort_unless(
            $business->status === 'active',
            403
        );

        $data = $request->validate([
            'branch_id' => [
                'required',
                'integer',

                Rule::exists(
                    'branches',
                    'id'
                )->where(
                    fn($query) =>
                    $query
                        ->where(
                            'business_id',
                            $business->id
                        )
                        ->where(
                            'status',
                            'active'
                        )
                ),
            ],
        ]);

        $branch = Branch::query()
            ->where(
                'id',
                $data['branch_id']
            )
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();

        $user = $request->user();

        if (! $user->isPlatformAdmin()) {
            $membership = BusinessUser::query()
                ->where(
                    'business_id',
                    $business->id
                )
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->firstOrFail();

            $hasAccess = $membership
                ->branches()
                ->where(
                    'branches.id',
                    $branch->id
                )
                ->exists();

            abort_unless(
                $hasAccess,
                403
            );
        }

        $request->session()->put(
            'current_business_id',
            $business->id
        );

        $request->session()->put(
            'current_branch_id',
            $branch->id
        );

        return redirect()
            ->route(
                'business.dashboard',
                $business
            )
            ->with(
                'success',
                'Sucursal seleccionada correctamente.'
            );
    }
}
