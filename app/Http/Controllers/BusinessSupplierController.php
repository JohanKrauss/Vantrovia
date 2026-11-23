<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessSupplierController extends Controller
{
    public function index(
        Request $request,
        Business $business
    ) {
        $search = trim(
            (string) $request->query('search', '')
        );

        $suppliers = $business
            ->suppliers()
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'name',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'legal_name',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'tax_id',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'phone',
                                    'ilike',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'businesses.suppliers.index',
            compact(
                'business',
                'suppliers',
                'search'
            )
        );
    }

    public function create(
        Business $business
    ) {
        return view(
            'businesses.suppliers.create',
            compact('business')
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $data = $this->validateSupplier(
            $request,
            $business
        );

        $data = $this->normalizeData($data);

        $data['is_active'] = true;

        $business
            ->suppliers()
            ->create($data);

        return redirect()
            ->route(
                'businesses.suppliers.index',
                $business
            )
            ->with(
                'success',
                'Proveedor creado correctamente.'
            );
    }

    public function edit(
        Business $business,
        Supplier $supplier
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $supplier
        );

        return view(
            'businesses.suppliers.edit',
            compact(
                'business',
                'supplier'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        Supplier $supplier
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $supplier
        );

        $data = $this->validateSupplier(
            $request,
            $business,
            $supplier
        );

        $supplier->update(
            $this->normalizeData($data)
        );

        return redirect()
            ->route(
                'businesses.suppliers.index',
                $business
            )
            ->with(
                'success',
                'Proveedor actualizado correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Business $business,
        Supplier $supplier
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $supplier
        );

        $data = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $supplier->update([
            'is_active' => $data['is_active'],
        ]);

        return back()->with(
            'success',
            $supplier->is_active
                ? 'Proveedor activado correctamente.'
                : 'Proveedor desactivado correctamente.'
        );
    }

    private function validateSupplier(
        Request $request,
        Business $business,
        ?Supplier $supplier = null
    ): array {
        return $request->validate([
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

            'tax_id' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'suppliers',
                    'tax_id'
                )
                    ->where(
                        fn($query) => $query->where(
                            'business_id',
                            $business->id
                        )
                    )
                    ->ignore($supplier?->id),
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);
    }

    private function normalizeData(
        array $data
    ): array {
        foreach (
            [
                'legal_name',
                'tax_id',
                'email',
                'phone',
                'address',
                'notes',
            ] as $field
        ) {
            if (
                !isset($data[$field])
                || trim((string) $data[$field]) === ''
            ) {
                $data[$field] = null;

                continue;
            }

            $data[$field] = trim(
                (string) $data[$field]
            );
        }

        $data['name'] = trim(
            $data['name']
        );

        return $data;
    }

    private function ensureBelongsToBusiness(
        Business $business,
        Supplier $supplier
    ): void {
        abort_unless(
            $supplier->business_id
                === $business->id,
            404
        );
    }
}
