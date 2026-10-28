<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::query()
            ->with('business')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhereHas('business', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $request->filled('business_id'),
                fn($query) => $query->where(
                    'business_id',
                    $request->business_id
                )
            )
            ->when(
                $request->filled('status'),
                fn($query) => $query->where(
                    'status',
                    $request->status
                )
            )
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $businesses = Business::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'branches.index',
            compact('branches', 'businesses')
        );
    }

    public function create()
    {
        $businesses = Business::query()
            ->where('status', '!=', 'cancelled')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'branches.create',
            compact('businesses')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => [
                'required',
                'exists:businesses,id',
            ],

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
        ]);

        $business = Business::findOrFail(
            $data['business_id']
        );

        $data['code'] = empty($data['code'])
            ? $this->generateUniqueCode(
                $business->id,
                $data['name']
            )
            : Str::lower($data['code']);

        $exists = Branch::where(
            'business_id',
            $business->id
        )
            ->where(
                'code',
                $data['code']
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'code' => 'Ese código ya existe dentro del negocio.',
            ]);
        }

        if (! empty($data['email'])) {
            $data['email'] = Str::lower(
                trim($data['email'])
            );
        }

        $data['country_code'] = 'MX';

        $data['is_main'] = ! Branch::where(
            'business_id',
            $business->id
        )->exists();

        Branch::create($data);

        return redirect()
            ->route('branches.index')
            ->with(
                'success',
                'Sucursal creada correctamente.'
            );
    }

    public function edit(Branch $branch)
    {
        $branch->load('business');

        return view(
            'branches.edit',
            compact('branch')
        );
    }

    public function update(
        Request $request,
        Branch $branch
    ) {
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

        $exists = Branch::where(
            'business_id',
            $branch->business_id
        )
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
                'code' => 'Ese código ya existe dentro del negocio.',
            ]);
        }

        if (
            $branch->is_main
            && $data['status'] === 'inactive'
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
            ->route('branches.index')
            ->with(
                'success',
                'Sucursal actualizada correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Branch $branch
    ) {
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
            $branch->is_main
            && $data['status'] === 'inactive'
        ) {
            return back()->with(
                'warning',
                'No puedes desactivar la sucursal principal. Primero asigna otra sucursal como principal.'
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

    public function makeMain(Branch $branch)
    {
        if ($branch->status !== 'active') {
            return back()->with(
                'warning',
                'Solo una sucursal activa puede establecerse como principal.'
            );
        }

        DB::transaction(function () use ($branch) {

            Branch::where(
                'business_id',
                $branch->business_id
            )->update([
                'is_main' => false,
            ]);

            $branch->update([
                'is_main' => true,
            ]);
        });

        return back()->with(
            'success',
            'Sucursal principal actualizada correctamente.'
        );
    }

    private function generateUniqueCode(
        int $businessId,
        string $name
    ): string {
        $base = Str::slug($name);

        if (empty($base)) {
            $base = 'sucursal';
        }

        $base = substr($base, 0, 60);

        $code = $base;
        $counter = 2;

        while (
            Branch::where(
                'business_id',
                $businessId
            )
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
