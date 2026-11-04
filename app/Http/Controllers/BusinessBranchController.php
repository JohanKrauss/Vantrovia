<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BusinessBranchController extends Controller
{
    public function index(Business $business)
    {
        $branches = $business
            ->branches()
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->paginate(10);

        return view(
            'businesses.branches.index',
            compact('business', 'branches')
        );
    }

    public function create(Business $business)
    {
        return view(
            'businesses.branches.create',
            compact('business')
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
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

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ], [
            'code.regex' =>
            'El código solo puede contener letras minúsculas, números y guiones.',
        ]);

        $data['code'] = empty($data['code'])
            ? $this->generateUniqueCode(
                $business,
                $data['name']
            )
            : Str::lower($data['code']);

        $exists = $business
            ->branches()
            ->where(
                'code',
                $data['code']
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'code' =>
                'Ese código ya existe dentro del negocio.',
            ]);
        }

        if (! empty($data['email'])) {
            $data['email'] = Str::lower(
                trim($data['email'])
            );
        }

        $data['country_code'] = 'MX';

        $data['is_main'] = ! $business
            ->branches()
            ->exists();

        $business
            ->branches()
            ->create($data);

        return redirect()
            ->route(
                'businesses.branches.index',
                $business
            )
            ->with(
                'success',
                'Sucursal creada correctamente.'
            );
    }

    public function edit(
        Business $business,
        Branch $branch
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $branch
        );

        return view(
            'businesses.branches.edit',
            compact(
                'business',
                'branch'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        Branch $branch
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $branch
        );

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
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

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $data['code'] = Str::lower(
            $data['code']
        );

        $exists = $business
            ->branches()
            ->where(
                'code',
                $data['code']
            )
            ->where(
                'id',
                '!=',
                $branch->id
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'code' =>
                'Ese código ya existe dentro del negocio.',
            ]);
        }

        if (
            $branch->is_main &&
            $data['status'] === 'inactive'
        ) {
            throw ValidationException::withMessages([
                'status' =>
                'La sucursal principal no puede desactivarse. Primero asigna otra como principal.',
            ]);
        }

        if (! empty($data['email'])) {
            $data['email'] = Str::lower(
                trim($data['email'])
            );
        }

        $branch->update($data);

        return redirect()
            ->route(
                'businesses.branches.index',
                $business
            )
            ->with(
                'success',
                'Sucursal actualizada correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Business $business,
        Branch $branch
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $branch
        );

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        if (
            $branch->is_main &&
            $data['status'] === 'inactive'
        ) {
            return back()->with(
                'warning',
                'No puedes desactivar la sucursal principal. Primero asigna otra como principal.'
            );
        }

        $branch->update([
            'status' => $data['status'],
        ]);

        return back()->with(
            'success',
            $branch->status === 'active'
                ? 'Sucursal activada correctamente.'
                : 'Sucursal desactivada correctamente.'
        );
    }

    public function makeMain(
        Business $business,
        Branch $branch
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $branch
        );

        if ($branch->status !== 'active') {
            return back()->with(
                'warning',
                'Solo una sucursal activa puede establecerse como principal.'
            );
        }

        DB::transaction(
            function () use (
                $business,
                $branch
            ) {
                $business
                    ->branches()
                    ->update([
                        'is_main' => false,
                    ]);

                $branch->update([
                    'is_main' => true,
                ]);
            }
        );

        return back()->with(
            'success',
            'Sucursal principal actualizada correctamente.'
        );
    }

    private function ensureBelongsToBusiness(
        Business $business,
        Branch $branch
    ): void {
        abort_unless(
            $branch->business_id === $business->id,
            404
        );
    }

    private function generateUniqueCode(
        Business $business,
        string $name
    ): string {
        $base = Str::slug($name);

        if (empty($base)) {
            $base = 'sucursal';
        }

        $base = substr(
            $base,
            0,
            60
        );

        $code = $base;
        $counter = 2;

        while (
            $business
            ->branches()
            ->where(
                'code',
                $code
            )
            ->exists()
        ) {
            $code = $base . '-' . $counter;
            $counter++;
        }

        return $code;
    }
}
