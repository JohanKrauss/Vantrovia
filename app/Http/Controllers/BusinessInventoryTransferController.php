<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryTransfer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BusinessInventoryTransferController extends Controller
{
    public function index(
        Business $business
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $transfers = InventoryTransfer::query()
            ->with([
                'sourceBranch',
                'destinationBranch',
                'user',
            ])
            ->withCount('items')
            ->where(
                'business_id',
                $business->id
            )
            ->where(function ($query) use ($branch) {
                $query
                    ->where(
                        'source_branch_id',
                        $branch->id
                    )
                    ->orWhere(
                        'destination_branch_id',
                        $branch->id
                    );
            })
            ->latest('transferred_at')
            ->paginate(20);

        return view(
            'businesses.inventory.transfers.index',
            compact(
                'business',
                'branch',
                'transfers'
            )
        );
    }

    public function create(
        Business $business
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        $destinationBranches = $business
            ->branches()
            ->where('status', 'active')
            ->where(
                'id',
                '!=',
                $branch->id
            )
            ->orderBy('name')
            ->get();

        $products = $business
            ->products()
            ->with([
                'unit',
                'inventories' => fn($query) => $query
                    ->where(
                        'branch_id',
                        $branch->id
                    ),
            ])
            ->where('is_active', true)
            ->whereHas(
                'inventories',
                fn($query) => $query
                    ->where(
                        'branch_id',
                        $branch->id
                    )
                    ->where(
                        'quantity',
                        '>',
                        0
                    )
            )
            ->orderBy('name')
            ->get();

        return view(
            'businesses.inventory.transfers.create',
            compact(
                'business',
                'branch',
                'destinationBranches',
                'products'
            )
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $sourceBranch = $this->getCurrentBranch(
            $business
        );

        $data = $request->validate([
            'destination_branch_id' => [
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

                Rule::notIn([
                    $sourceBranch->id,
                ]),
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

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $destinationBranch = Branch::query()
            ->whereKey(
                $data['destination_branch_id']
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

        $transfer = DB::transaction(
            function () use (
                $business,
                $sourceBranch,
                $destinationBranch,
                $data
            ) {
                /*
                 * Ordenamos por product_id para mantener
                 * un orden consistente durante los bloqueos.
                 */
                $items = collect(
                    $data['items']
                )
                    ->sortBy('product_id')
                    ->values();

                $transfer = InventoryTransfer::create([
                    'business_id' => $business->id,

                    'source_branch_id' =>
                    $sourceBranch->id,

                    'destination_branch_id' =>
                    $destinationBranch->id,

                    'user_id' => auth()->id(),

                    'status' => 'completed',

                    'notes' =>
                    $data['notes'] ?? null,

                    'transferred_at' => now(),
                ]);

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

                    $sourceInventory = Inventory::query()
                        ->where(
                            'business_id',
                            $business->id
                        )
                        ->where(
                            'branch_id',
                            $sourceBranch->id
                        )
                        ->where(
                            'product_id',
                            $product->id
                        )
                        ->lockForUpdate()
                        ->first();

                    $quantity = $this->toThousandths(
                        $item['quantity']
                    );

                    $sourcePrevious =
                        $sourceInventory
                        ? $this->toThousandths(
                            $sourceInventory->quantity
                        )
                        : 0;

                    if (
                        ! $sourceInventory
                        || $sourcePrevious < $quantity
                    ) {
                        throw ValidationException::withMessages([
                            'items' =>
                            "No hay existencia suficiente de {$product->name} en {$sourceBranch->name}.",
                        ]);
                    }

                    $destinationInventory =
                        Inventory::firstOrCreate(
                            [
                                'business_id' =>
                                $business->id,

                                'branch_id' =>
                                $destinationBranch->id,

                                'product_id' =>
                                $product->id,
                            ],
                            [
                                'quantity' => 0,
                                'minimum_stock' => 0,
                            ]
                        );

                    $destinationInventory =
                        Inventory::query()
                        ->whereKey(
                            $destinationInventory->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                    $destinationPrevious =
                        $this->toThousandths(
                            $destinationInventory->quantity
                        );

                    $sourceNew =
                        $sourcePrevious - $quantity;

                    $destinationNew =
                        $destinationPrevious + $quantity;

                    $quantityValue =
                        $this->fromThousandths(
                            $quantity
                        );

                    $sourcePreviousValue =
                        $this->fromThousandths(
                            $sourcePrevious
                        );

                    $sourceNewValue =
                        $this->fromThousandths(
                            $sourceNew
                        );

                    $destinationPreviousValue =
                        $this->fromThousandths(
                            $destinationPrevious
                        );

                    $destinationNewValue =
                        $this->fromThousandths(
                            $destinationNew
                        );

                    $sourceInventory->update([
                        'quantity' =>
                        $sourceNewValue,
                    ]);

                    $destinationInventory->update([
                        'quantity' =>
                        $destinationNewValue,
                    ]);

                    $transfer->items()->create([
                        'product_id' =>
                        $product->id,

                        'quantity' =>
                        $quantityValue,
                    ]);

                    InventoryMovement::create([
                        'inventory_id' =>
                        $sourceInventory->id,

                        'user_id' =>
                        auth()->id(),

                        'type' =>
                        'transfer_out',

                        'quantity' =>
                        $quantityValue,

                        'previous_quantity' =>
                        $sourcePreviousValue,

                        'new_quantity' =>
                        $sourceNewValue,

                        'reference_type' =>
                        'inventory_transfer',

                        'reference_id' =>
                        $transfer->id,

                        'notes' =>
                        "Traspaso a {$destinationBranch->name}",
                    ]);

                    InventoryMovement::create([
                        'inventory_id' =>
                        $destinationInventory->id,

                        'user_id' =>
                        auth()->id(),

                        'type' =>
                        'transfer_in',

                        'quantity' =>
                        $quantityValue,

                        'previous_quantity' =>
                        $destinationPreviousValue,

                        'new_quantity' =>
                        $destinationNewValue,

                        'reference_type' =>
                        'inventory_transfer',

                        'reference_id' =>
                        $transfer->id,

                        'notes' =>
                        "Traspaso desde {$sourceBranch->name}",
                    ]);
                }

                return $transfer;
            }
        );

        return redirect()
            ->route(
                'businesses.inventory.transfers.show',
                [
                    $business,
                    $transfer,
                ]
            )
            ->with(
                'success',
                'Traspaso realizado correctamente.'
            );
    }

    public function show(
        Business $business,
        InventoryTransfer $transfer
    ) {
        $branch = $this->getCurrentBranch(
            $business
        );

        abort_unless(
            $transfer->business_id
                === $business->id,
            404
        );

        /*
         * El traspaso debe involucrar
         * a la sucursal activa.
         */
        abort_unless(
            $transfer->source_branch_id
                === $branch->id
                || $transfer->destination_branch_id
                === $branch->id,
            404
        );

        $transfer->load([
            'sourceBranch',
            'destinationBranch',
            'user',
            'items.product.unit',
        ]);

        return view(
            'businesses.inventory.transfers.show',
            compact(
                'business',
                'branch',
                'transfer'
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
                'Debes seleccionar una sucursal.'
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
