<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BusinessInventoryController extends Controller
{
    public function index(
        Request $request,
        Business $business
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $products = $business
            ->products()
            ->with([
                'category',
                'unit',
                'inventories' => fn($query) => $query
                    ->where(
                        'branch_id',
                        $branch->id
                    ),
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
            ->paginate(20)
            ->withQueryString();

        return view(
            'businesses.inventory.index',
            compact(
                'business',
                'branch',
                'products',
                'search'
            )
        );
    }

    public function adjust(
        Request $request,
        Business $business,
        Product $product
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $this->ensureProductBelongsToBusiness(
            $business,
            $product
        );

        abort_unless(
            $product->is_active,
            422,
            'No se puede ajustar el inventario de un producto inactivo.'
        );

        $data = $request->validate([
            'type' => [
                'required',
                Rule::in([
                    'adjustment_in',
                    'adjustment_out',
                ]),
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999999.999',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(
            function () use (
                $business,
                $branch,
                $product,
                $data
            ) {
                $inventory = Inventory::firstOrCreate(
                    [
                        'business_id' => $business->id,
                        'branch_id' => $branch->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => 0,
                        'minimum_stock' => 0,
                    ]
                );

                $inventory = Inventory::query()
                    ->whereKey($inventory->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $previousQuantity = $this->toThousandths(
                    $inventory->quantity
                );

                $adjustmentQuantity = $this->toThousandths(
                    $data['quantity']
                );

                if (
                    $data['type']
                    === 'adjustment_in'
                ) {
                    $newQuantity =
                        $previousQuantity
                        + $adjustmentQuantity;
                } else {
                    $newQuantity =
                        $previousQuantity
                        - $adjustmentQuantity;
                }

                if ($newQuantity < 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'La salida no puede ser mayor que la existencia actual.',
                    ]);
                }

                $previousValue = $this->fromThousandths(
                    $previousQuantity
                );

                $adjustmentValue = $this->fromThousandths(
                    $adjustmentQuantity
                );

                $newValue = $this->fromThousandths(
                    $newQuantity
                );

                $inventory->update([
                    'quantity' => $newValue,
                ]);

                InventoryMovement::create([
                    'inventory_id' => $inventory->id,

                    'user_id' => auth()->id(),

                    'type' => $data['type'],

                    'quantity' => $adjustmentValue,

                    'previous_quantity' => $previousValue,

                    'new_quantity' => $newValue,

                    'notes' => $data['notes'] ?? null,
                ]);
            }
        );

        return back()->with(
            'success',
            $data['type'] === 'adjustment_in'
                ? 'Entrada de inventario registrada correctamente.'
                : 'Salida de inventario registrada correctamente.'
        );
    }

    public function updateMinimumStock(
        Request $request,
        Business $business,
        Product $product
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $this->ensureProductBelongsToBusiness(
            $business,
            $product
        );

        $data = $request->validate([
            'minimum_stock' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999999.999',
            ],
        ]);

        Inventory::updateOrCreate(
            [
                'business_id' => $business->id,
                'branch_id' => $branch->id,
                'product_id' => $product->id,
            ],
            [
                'minimum_stock' => $this->fromThousandths(
                    $this->toThousandths(
                        $data['minimum_stock']
                    )
                ),
            ]
        );

        return back()->with(
            'success',
            'Stock mínimo actualizado correctamente.'
        );
    }

    public function movements(
        Business $business
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $movements = InventoryMovement::query()
            ->with([
                'inventory.product.unit',
                'user',
            ])
            ->whereHas(
                'inventory',
                fn($query) => $query
                    ->where(
                        'business_id',
                        $business->id
                    )
                    ->where(
                        'branch_id',
                        $branch->id
                    )
            )
            ->latest()
            ->paginate(30);

        return view(
            'businesses.inventory.movements',
            compact(
                'business',
                'branch',
                'movements'
            )
        );
    }

    private function getCurrentBranch(
        Business $business
    ): Branch {
        $branchId = session(
            'current_branch_id'
        );

        if (! $branchId) {
            abort(
                403,
                'Debes seleccionar una sucursal antes de acceder al inventario.'
            );
        }

        $branch = Branch::query()
            ->whereKey($branchId)
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'status',
                'active'
            )
            ->first();

        if (! $branch) {
            session()->forget(
                'current_branch_id'
            );

            abort(
                403,
                'La sucursal seleccionada no está disponible.'
            );
        }

        return $branch;
    }

    private function ensureProductBelongsToBusiness(
        Business $business,
        Product $product
    ): void {
        abort_unless(
            $product->business_id
                === $business->id,
            404
        );
    }

    private function toThousandths(
        mixed $value
    ): int {
        return (int) round(
            (float) $value * 1000
        );
    }

    private function fromThousandths(
        int $value
    ): string {
        return number_format(
            $value / 1000,
            3,
            '.',
            ''
        );
    }
}
