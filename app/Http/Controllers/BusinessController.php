<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BusinessController extends Controller
{
    private const RESERVED_SLUGS = [
        'admin',
        'www',
        'api',
        'app',
        'mail',
        'support',
        'status',
    ];

    public function index(Request $request)
    {
        $businesses = Business::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('legal_name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('status'),
                fn($query) => $query->where('status', $request->status)
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('businesses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'slug' => [
                'nullable',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:businesses,slug',
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

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'active',
                    'suspended',
                    'cancelled',
                ]),
            ],
        ], [
            'slug.regex' => 'El subdominio solo puede contener letras minúsculas, números y guiones.',
            'slug.unique' => 'Ese subdominio ya está siendo utilizado.',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug(
                $data['name']
            );
        }

        $data['slug'] = Str::lower($data['slug']);

        $this->validateReservedSlug(
            $data['slug']
        );

        if (! empty($data['email'])) {
            $data['email'] = Str::lower(
                trim($data['email'])
            );
        }

        $data['timezone'] = 'America/Mexico_City';
        $data['currency'] = 'MXN';

        Business::create($data);

        return redirect()
            ->route('businesses.index')
            ->with(
                'success',
                'Negocio creado correctamente.'
            );
    }

    public function edit(Business $business)
    {
        return view(
            'businesses.edit',
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

            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                Rule::unique(
                    'businesses',
                    'slug'
                )->ignore($business->id),
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

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'active',
                    'suspended',
                    'cancelled',
                ]),
            ],
        ]);

        $data['slug'] = Str::lower(
            $data['slug']
        );

        $this->validateReservedSlug(
            $data['slug']
        );

        if (! empty($data['email'])) {
            $data['email'] = Str::lower(
                trim($data['email'])
            );
        }

        $business->update($data);

        return redirect()
            ->route('businesses.index')
            ->with(
                'success',
                'Negocio actualizado correctamente.'
            );
    }

    public function updateStatus(
        Request $request,
        Business $business
    ) {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'active',
                    'suspended',
                    'cancelled',
                ]),
            ],
        ]);

        $business->update([
            'status' => $data['status'],
        ]);

        $messages = [
            'active' => 'Negocio activado correctamente.',
            'pending' => 'Negocio marcado como pendiente.',
            'suspended' => 'Negocio suspendido correctamente.',
            'cancelled' => 'Negocio cancelado correctamente.',
        ];

        return back()->with(
            'success',
            $messages[$data['status']]
        );
    }

    private function generateUniqueSlug(
        string $name
    ): string {
        $base = Str::slug($name);

        if (empty($base)) {
            $base = 'negocio';
        }

        $base = substr($base, 0, 60);

        $slug = $base;
        $counter = 2;

        while (
            Business::where('slug', $slug)->exists()
            || in_array($slug, self::RESERVED_SLUGS, true)
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function validateReservedSlug(
        string $slug
    ): void {
        if (
            in_array(
                $slug,
                self::RESERVED_SLUGS,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'slug' => 'Ese subdominio está reservado por Vantrovia.',
            ]);
        }
    }
}
