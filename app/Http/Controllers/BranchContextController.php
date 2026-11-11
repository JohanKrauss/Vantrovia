<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $branches = $this->getAvailableBranches(
            $business,
            $user
        );

        /*
         * Si solo tiene una sucursal disponible,
         * se selecciona automáticamente.
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
                    fn($query) => $query
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

        /*
         * Platform Admin puede entrar a cualquier
         * sucursal activa.
         */
        if (! $user->isPlatformAdmin()) {
            $membership = $this->getMembership(
                $business,
                $user->id
            );

            /*
             * El Administrador del negocio tiene
             * acceso automático a todas sus sucursales.
             */
            $isBusinessAdmin =
                $membership->role?->slug === 'administrator';

            if (! $isBusinessAdmin) {
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

    private function getAvailableBranches(
        Business $business,
        $user
    ): Collection {
        /*
         * Platform Admin:
         * todas las sucursales activas.
         */
        if ($user->isPlatformAdmin()) {
            return $business
                ->branches()
                ->where('status', 'active')
                ->orderByDesc('is_main')
                ->orderBy('name')
                ->get();
        }

        $membership = $this->getMembership(
            $business,
            $user->id
        );

        /*
         * Administrador del negocio:
         * automáticamente todas las sucursales.
         */
        if (
            $membership->role?->slug === 'administrator'
        ) {
            return $business
                ->branches()
                ->where('status', 'active')
                ->orderByDesc('is_main')
                ->orderBy('name')
                ->get();
        }

        /*
         * Otros roles:
         * únicamente sucursales asignadas.
         */
        return $membership
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

    private function getMembership(
        Business $business,
        int $userId
    ): BusinessUser {
        return BusinessUser::query()
            ->with('role')
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'user_id',
                $userId
            )
            ->where(
                'is_active',
                true
            )
            ->firstOrFail();
    }
}
