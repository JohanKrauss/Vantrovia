<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BusinessPurchaseController extends Controller
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

        $purchases = Purchase::query()
            ->with([
                'supplier',
                'user',
            ])
            ->withCount('items')
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'branch_id',
                $branch->id
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'reference',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'supplier',
                                    fn($query) => $query->where(
                                        'name',
                                        'ilike',
                                        "%{$search}%"
                                    )
                                )
                                ->orWhereHas(
                                    'items.product',
                                    fn($query) => $query->where(
                                        'name',
                                        'ilike',
                                        "%{$search}%"
                                    )
                                );
                        }
                    );
                }
            )
            ->latest('purchased_at')
            ->paginate(20)
            ->withQueryString();

        return view(
            'businesses.purchases.index',
            compact(
                'business',
                'branch',
                'purchases',
                'search'
            )
        );
    }

    public function create(
        Business $business
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $suppliers = $business
            ->suppliers()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = $business
            ->products()
            ->with('unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'businesses.purchases.create',
            compact(
                'business',
                'branch',
                'suppliers',
                'products'
            )
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $data = $request->validate([
            'supplier_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'suppliers',
                    'id'
                )->where(
                    fn($query) => $query
                        ->where(
                            'business_id',
                            $business->id
                        )
                        ->where(
                            'is_active',
                            true
                        )
                ),
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'purchased_at' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',

                Rule::exists(
                    'products',
                    'id'
                )->where(
                    fn($query) => $query
                        ->where(
                            'business_id',
                            $business->id
                        )
                        ->where(
                            'is_active',
                            true
                        )
                ),
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999999.999',
            ],

            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
        ]);

        $purchase = DB::transaction(
            function () use (
                $business,
                $branch,
                $data
            ) {
                /*
                 * Orden consistente para los bloqueos
                 * de inventario.
                 */
                $items = collect(
                    $data['items']
                )
                    ->sortBy('product_id')
                    ->values();

                $purchase = Purchase::create([
                    'business_id' =>
                    $business->id,

                    'branch_id' =>
                    $branch->id,

                    'supplier_id' =>
                    $data['supplier_id'] ?? null,

                    'user_id' =>
                    auth()->id(),

                    'reference' =>
                    $this->normalizeNullable(
                        $data['reference'] ?? null
                    ),

                    'status' =>
                    'completed',

                    'total' =>
                    '0.00',

                    'notes' =>
                    $this->normalizeNullable(
                        $data['notes'] ?? null
                    ),

                    'purchased_at' =>
                    $data['purchased_at'],
                ]);

                $totalCents = 0;

                foreach ($items as $item) {
                    $product = Product::query()
                        ->whereKey(
                            $item['product_id']
                        )
                        ->where(
                            'business_id',
                            $business->id
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->firstOrFail();

                    $quantity =
                        $this->toThousandths(
                            $item['quantity']
                        );

                    $unitCost =
                        $this->toCents(
                            $item['unit_cost']
                        );

                    /*
                     * quantity usa milésimas.
                     *
                     * Ejemplo:
                     * 2.500 x $10.00
                     * = $25.00
                     */
                    $subtotal =
                        (int) round(
                            (
                                $quantity
                                * $unitCost
                            ) / 1000
                        );

                    $totalCents += $subtotal;

                    $purchase
                        ->items()
                        ->create([
                            'product_id' =>
                            $product->id,

                            'quantity' =>
                            $this->fromThousandths(
                                $quantity
                            ),

                            'unit_cost' =>
                            $this->fromCents(
                                $unitCost
                            ),

                            'subtotal' =>
                            $this->fromCents(
                                $subtotal
                            ),
                        ]);

                    $inventory =
                        Inventory::firstOrCreate(
                            [
                                'business_id' =>
                                $business->id,

                                'branch_id' =>
                                $branch->id,

                                'product_id' =>
                                $product->id,
                            ],
                            [
                                'quantity' => 0,
                                'minimum_stock' => 0,
                            ]
                        );

                    $inventory =
                        Inventory::query()
                        ->whereKey(
                            $inventory->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                    $previousQuantity =
                        $this->toThousandths(
                            $inventory->quantity
                        );

                    $newQuantity =
                        $previousQuantity
                        + $quantity;

                    $previousValue =
                        $this->fromThousandths(
                            $previousQuantity
                        );

                    $quantityValue =
                        $this->fromThousandths(
                            $quantity
                        );

                    $newValue =
                        $this->fromThousandths(
                            $newQuantity
                        );

                    $inventory->update([
                        'quantity' =>
                        $newValue,
                    ]);

                    InventoryMovement::create([
                        'inventory_id' =>
                        $inventory->id,

                        'user_id' =>
                        auth()->id(),

                        'type' =>
                        'purchase',

                        'quantity' =>
                        $quantityValue,

                        'previous_quantity' =>
                        $previousValue,

                        'new_quantity' =>
                        $newValue,

                        'reference_type' =>
                        'purchase',

                        'reference_id' =>
                        $purchase->id,

                        'notes' =>
                        "Compra #{$purchase->id}",
                    ]);
                }

                $purchase->update([
                    'total' =>
                    $this->fromCents(
                        $totalCents
                    ),
                ]);

                return $purchase;
            }
        );

        return redirect()
            ->route(
                'businesses.purchases.show',
                [
                    $business,
                    $purchase,
                ]
            )
            ->with(
                'success',
                'Compra registrada correctamente.'
            );
    }

    public function show(
        Business $business,
        Purchase $purchase
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        abort_unless(
            $purchase->business_id
                === $business->id,
            404
        );

        abort_unless(
            $purchase->branch_id
                === $branch->id,
            404
        );

        $purchase->load([
            'branch',
            'supplier',
            'user',
            'items.product.unit',
        ]);

        return view(
            'businesses.purchases.show',
            compact(
                'business',
                'branch',
                'purchase'
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
                'Debes seleccionar una sucursal antes de acceder a Compras.'
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

    private function toCents(
        mixed $value
    ): int {
        return (int) round(
            (float) $value * 100
        );
    }

    private function fromCents(
        int $value
    ): string {
        return number_format(
            $value / 100,
            2,
            '.',
            ''
        );
    }
}
