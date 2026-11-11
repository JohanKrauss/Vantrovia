<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessUnitController extends Controller
{
    public function index(Business $business)
    {
        $units = $business
            ->units()
            ->orderBy('name')
            ->paginate(10);

        return view(
            'businesses.units.index',
            compact('business', 'units')
        );
    }

    public function create(Business $business)
    {
        return view(
            'businesses.units.create',
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
            'symbol' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $data['slug'] = $this->generateUniqueSlug(
            $business,
            $data['name']
        );

        $data['is_active'] = true;

        $business
            ->units()
            ->create($data);

        return redirect()
            ->route(
                'businesses.units.index',
                $business
            )
            ->with(
                'success',
                'Unidad de medida creada correctamente.'
            );
    }

    public function edit(
        Business $business,
        Unit $unit
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $unit
        );

        return view(
            'businesses.units.edit',
            compact(
                'business',
                'unit'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        Unit $unit
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $unit
        );

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'symbol' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        if ($unit->name !== $data['name']) {
            $data['slug'] = $this->generateUniqueSlug(
                $business,
                $data['name'],
                $unit->id
            );
        }

        $unit->update($data);

        return redirect()
            ->route(
                'businesses.units.index',
                $business
            )
            ->with(
                'success',
                'Unidad de medida actualizada correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Business $business,
        Unit $unit
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $unit
        );

        $data = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $unit->update([
            'is_active' => $data['is_active'],
        ]);

        return back()->with(
            'success',
            $unit->is_active
                ? 'Unidad activada correctamente.'
                : 'Unidad desactivada correctamente.'
        );
    }

    private function ensureBelongsToBusiness(
        Business $business,
        Unit $unit
    ): void {
        abort_unless(
            $unit->business_id === $business->id,
            404
        );
    }

    private function generateUniqueSlug(
        Business $business,
        string $name,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name);

        if (empty($base)) {
            $base = 'unidad';
        }

        $slug = $base;
        $counter = 2;

        while (
            $business
            ->units()
            ->where('slug', $slug)
            ->when(
                $ignoreId,
                fn($query) => $query->where(
                    'id',
                    '!=',
                    $ignoreId
                )
            )
            ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
