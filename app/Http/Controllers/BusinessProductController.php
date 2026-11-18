<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessProductController extends Controller
{
    public function index(
        Request $request,
        Business $business
    ) {
        $search = trim(
            (string) $request->query('search', '')
        );

        $products = $business
            ->products()
            ->with([
                'category',
                'unit',
            ])
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
                                    'sku',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'barcode',
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
            'businesses.products.index',
            compact(
                'business',
                'products',
                'search'
            )
        );
    }

    public function create(Business $business)
    {
        $categories = $business
            ->categories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $units = $business
            ->units()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'businesses.products.create',
            compact(
                'business',
                'categories',
                'units'
            )
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $data = $this->validateProduct(
            $request,
            $business
        );

        $data['sku'] = $this->normalizeNullable(
            $data['sku'] ?? null
        );

        $data['barcode'] = $this->normalizeNullable(
            $data['barcode'] ?? null
        );

        $data['description'] = $this->normalizeNullable(
            $data['description'] ?? null
        );

        $data['is_active'] = true;

        $business
            ->products()
            ->create($data);

        return redirect()
            ->route(
                'businesses.products.index',
                $business
            )
            ->with(
                'success',
                'Producto creado correctamente.'
            );
    }

    public function edit(
        Business $business,
        Product $product
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $product
        );

        /*
         * Mostramos elementos activos y también
         * la categoría/unidad actual aunque haya sido
         * desactivada después de crear el producto.
         */
        $categories = $business
            ->categories()
            ->where(
                function ($query) use ($product) {
                    $query
                        ->where('is_active', true)
                        ->when(
                            $product->category_id,
                            fn ($query) => $query->orWhere(
                                'id',
                                $product->category_id
                            )
                        );
                }
            )
            ->orderBy('name')
            ->get();

        $units = $business
            ->units()
            ->where(
                function ($query) use ($product) {
                    $query
                        ->where('is_active', true)
                        ->when(
                            $product->unit_id,
                            fn ($query) => $query->orWhere(
                                'id',
                                $product->unit_id
                            )
                        );
                }
            )
            ->orderBy('name')
            ->get();

        return view(
            'businesses.products.edit',
            compact(
                'business',
                'product',
                'categories',
                'units'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        Product $product
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $product
        );

        $data = $this->validateProduct(
            $request,
            $business,
            $product
        );

        $data['sku'] = $this->normalizeNullable(
            $data['sku'] ?? null
        );

        $data['barcode'] = $this->normalizeNullable(
            $data['barcode'] ?? null
        );

        $data['description'] = $this->normalizeNullable(
            $data['description'] ?? null
        );

        $product->update($data);

        return redirect()
            ->route(
                'businesses.products.index',
                $business
            )
            ->with(
                'success',
                'Producto actualizado correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Business $business,
        Product $product
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $product
        );

        $data = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $product->update([
            'is_active' => $data['is_active'],
        ]);

        return back()->with(
            'success',
            $product->is_active
                ? 'Producto activado correctamente.'
                : 'Producto desactivado correctamente.'
        );
    }

    private function validateProduct(
        Request $request,
        Business $business,
        ?Product $product = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'categories',
                    'id'
                )->where(
                    fn ($query) => $query->where(
                        'business_id',
                        $business->id
                    )
                ),
            ],

            'unit_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'units',
                    'id'
                )->where(
                    fn ($query) => $query->where(
                        'business_id',
                        $business->id
                    )
                ),
            ],

            'sku' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'products',
                    'sku'
                )
                    ->where(
                        fn ($query) => $query->where(
                            'business_id',
                            $business->id
                        )
                    )
                    ->ignore($product?->id),
            ],

            'barcode' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'products',
                    'barcode'
                )
                    ->where(
                        fn ($query) => $query->where(
                            'business_id',
                            $business->id
                        )
                    )
                    ->ignore($product?->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'cost' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],

            'sale_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
        ]);
    }

    private function ensureBelongsToBusiness(
        Business $business,
        Product $product
    ): void {
        abort_unless(
            $product->business_id === $business->id,
            404
        );
    }

    private function normalizeNullable(
        ?string $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === ''
            ? null
            : $value;
    }
}