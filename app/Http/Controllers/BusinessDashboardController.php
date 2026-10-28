<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessDashboardController extends Controller
{
    public function index(
        Request $request,
        Business $business
    ) {
        $request->session()->put(
            'current_business_id',
            $business->id
        );

        $branchCount = $business
            ->branches()
            ->count();

        $activeBranchCount = $business
            ->branches()
            ->where('status', 'active')
            ->count();

        $userCount = $business
            ->users()
            ->wherePivot('is_active', true)
            ->count();

        $roleName = null;

        if (! $request->user()->isPlatformAdmin()) {
            $membership = DB::table('business_user')
                ->where(
                    'business_id',
                    $business->id
                )
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->first();

            if ($membership?->role_id) {
                $roleName = Role::where(
                    'id',
                    $membership->role_id
                )
                    ->where(
                        'business_id',
                        $business->id
                    )
                    ->value('name');
            }
        }

        return view(
            'businesses.dashboard',
            compact(
                'business',
                'branchCount',
                'activeBranchCount',
                'userCount',
                'roleName'
            )
        );
    }
}
