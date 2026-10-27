@php
$editing = isset($role);

$selectedPermissions = old(
'permissions',
$editing
? $role->permissions->pluck('id')->toArray()
: []
);

$moduleLabels = [
'users' => 'Usuarios',
'branches' => 'Sucursales',
'roles' => 'Roles y permisos',
'business' => 'Negocio',
];
@endphp

<div class="card-body">

    <div class="row">

        <div class="col-md-7">

            <div class="mb-3">

                <label class="form-label">
                    Nombre del rol
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $role->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Ej. Cajero"
                    required>

                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        <div class="col-md-5">

            <div class="mb-3">

                <label class="form-label">
                    Identificador
                </label>

                <input
                    type="text"
                    name="slug"
                    value="{{ old('slug', $role->slug ?? '') }}"
                    class="form-control @error('slug') is-invalid @enderror"
                    placeholder="cajero"
                    {{ $editing ? 'required' : '' }}>

                @error('slug')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

                @if(!$editing)
                <small class="form-hint">
                    Puedes dejarlo vacío y Vantrovia lo generará.
                </small>
                @endif

            </div>

        </div>

    </div>


    <hr>


    <div class="mb-3">

        <h3 class="card-title mb-1">
            Permisos
        </h3>

        <div class="text-secondary">
            Selecciona las acciones que podrá realizar este rol.
        </div>

    </div>


    <div class="row row-cards">

        @foreach($permissions as $module => $modulePermissions)

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">

                        {{ $moduleLabels[$module] ?? ucfirst($module) }}

                    </h3>

                </div>


                <div class="card-body">

                    @foreach($modulePermissions as $permission)

                    <label class="form-check mb-3">

                        <input
                            type="checkbox"
                            class="form-check-input"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            @checked(
                            in_array(
                            $permission->id,
                        $selectedPermissions
                        )
                        )
                        >

                        <span class="form-check-label">

                            <strong>
                                {{ $permission->name }}
                            </strong>

                            @if($permission->description)

                            <span class="form-check-description">
                                {{ $permission->description }}
                            </span>

                            @endif

                        </span>

                    </label>

                    @endforeach

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>