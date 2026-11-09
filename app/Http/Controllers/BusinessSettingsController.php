<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BusinessSettingsController extends Controller
{
    public function edit(
        Request $request,
        Business $business
    ) {
        $request->session()->put(
            'current_business_id',
            $business->id
        );

        return view(
            'businesses.settings.edit',
            compact('business')
        );
    }

    public function update(
        Request $request,
        Business $business
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'legal_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'timezone' => [
                'required',
                'timezone',
            ],

            'currency' => [
                'required',
                Rule::in([
                    'MXN',
                    'USD',
                ]),
            ],
        ]);

        if (! empty($data['email'])) {
            $data['email'] = Str::lower(
                trim($data['email'])
            );
        }

        $business->update($data);

        $request->session()->put(
            'current_business_id',
            $business->id
        );

        return redirect()
            ->route(
                'businesses.settings.edit',
                $business
            )
            ->with(
                'success',
                'Configuración actualizada correctamente.'
            );
    }
}
