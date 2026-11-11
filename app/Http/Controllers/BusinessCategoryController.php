<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessCategoryController extends Controller
{
    public function index(Business $business)
    {
        $categories = $business
            ->categories()
            ->orderBy('name')
            ->paginate(10);

        return view(
            'businesses.categories.index',
            compact('business', 'categories')
        );
    }

    public function create(Business $business)
    {
        return view(
            'businesses.categories.create',
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
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $data['slug'] = $this->generateUniqueSlug(
            $business,
            $data['name']
        );

        $data['is_active'] = true;

        $business
            ->categories()
            ->create($data);

        return redirect()
            ->route(
                'businesses.categories.index',
                $business
            )
            ->with(
                'success',
                'Categoría creada correctamente.'
            );
    }

    public function edit(
        Business $business,
        Category $category
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $category
        );

        return view(
            'businesses.categories.edit',
            compact(
                'business',
                'category'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        Category $category
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $category
        );

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        if ($category->name !== $data['name']) {
            $data['slug'] = $this->generateUniqueSlug(
                $business,
                $data['name'],
                $category->id
            );
        }

        $category->update($data);

        return redirect()
            ->route(
                'businesses.categories.index',
                $business
            )
            ->with(
                'success',
                'Categoría actualizada correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Business $business,
        Category $category
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $category
        );

        $data = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $category->update([
            'is_active' => $data['is_active'],
        ]);

        return back()->with(
            'success',
            $category->is_active
                ? 'Categoría activada correctamente.'
                : 'Categoría desactivada correctamente.'
        );
    }

    private function ensureBelongsToBusiness(
        Business $business,
        Category $category
    ): void {
        abort_unless(
            $category->business_id === $business->id,
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
            $base = 'categoria';
        }

        $slug = $base;
        $counter = 2;

        while (
            $business
            ->categories()
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
