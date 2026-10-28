<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusinessContextController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isPlatformAdmin()) {
            return redirect()->route('dashboard');
        }

        $businesses = $user
            ->businesses()
            ->wherePivot('is_active', true)
            ->where('businesses.status', 'active')
            ->orderBy('businesses.name')
            ->get();

        return view(
            'business.select',
            compact('businesses')
        );
    }

    public function switch(Request $request)
    {
        $user = $request->user();

        if ($user->isPlatformAdmin()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'business_id' => [
                'required',
                'integer',
            ],
        ]);

        $business = $user
            ->businesses()
            ->wherePivot('is_active', true)
            ->where('businesses.status', 'active')
            ->where(
                'businesses.id',
                $data['business_id']
            )
            ->first();

        if (! $business) {
            return back()->with(
                'warning',
                'No tienes acceso a ese negocio.'
            );
        }

        $request->session()->put(
            'current_business_id',
            $business->id
        );

        return redirect()->route(
            'business.dashboard',
            $business
        );
    }
}
