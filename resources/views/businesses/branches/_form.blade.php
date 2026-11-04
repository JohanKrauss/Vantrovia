@php
$editing = isset($branch);
@endphp


<div class="card-body">

    {{-- Negocio --}}
    <div class="mb-3">

        <label class="form-label">
            Negocio
        </label>

        <input
            type="text"
            class="form-control"
            value="{{ $business->name }}"
            disabled>

        <small class="form-hint">
            La sucursal será creada dentro de este negocio.
        </small>

    </div>


    <div class="row">

        {{-- Nombre --}}
        <div class="col-md-8">

            <div class="mb-3">

                <label class="form-label">
                    Nombre de la sucursal
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $branch->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Ej. Sucursal Centro"
                    required>

                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        {{-- Código --}}
        <div class="col-md-4">

            <div class="mb-3">

                <label class="form-label">
                    Código
                </label>

                <input
                    type="text"
                    name="code"
                    value="{{ old('code', $branch->code ?? '') }}"
                    class="form-control @error('code') is-invalid @enderror"
                    placeholder="centro"
                    {{ $editing ? 'required' : '' }}>

                @error('code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

                @if(!$editing)

                <small class="form-hint">
                    Déjalo vacío para generarlo automáticamente.
                </small>

                @endif

            </div>

        </div>

    </div>


    <div class="row">

        {{-- Email --}}
        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Correo
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $branch->email ?? '') }}"
                    class="form-control @error('email') is-invalid @enderror">

                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        {{-- Teléfono --}}
        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Teléfono
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $branch->phone ?? '') }}"
                    class="form-control @error('phone') is-invalid @enderror">

                @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>

    </div>


    {{-- Dirección --}}
    <div class="mb-3">

        <label class="form-label">
            Dirección
        </label>

        <input
            type="text"
            name="address_line_1"
            value="{{ old('address_line_1', $branch->address_line_1 ?? '') }}"
            class="form-control @error('address_line_1') is-invalid @enderror"
            placeholder="Calle, número, etc.">

        @error('address_line_1')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="mb-3">

        <label class="form-label">
            Información adicional
        </label>

        <input
            type="text"
            name="address_line_2"
            value="{{ old('address_line_2', $branch->address_line_2 ?? '') }}"
            class="form-control"
            placeholder="Colonia, interior, referencias...">

    </div>


    <div class="row">

        {{-- Ciudad --}}
        <div class="col-md-5">

            <div class="mb-3">

                <label class="form-label">
                    Ciudad
                </label>

                <input
                    type="text"
                    name="city"
                    value="{{ old('city', $branch->city ?? '') }}"
                    class="form-control">

            </div>

        </div>


        {{-- Estado --}}
        <div class="col-md-5">

            <div class="mb-3">

                <label class="form-label">
                    Estado
                </label>

                <input
                    type="text"
                    name="state"
                    value="{{ old('state', $branch->state ?? '') }}"
                    class="form-control">

            </div>

        </div>


        {{-- CP --}}
        <div class="col-md-2">

            <div class="mb-3">

                <label class="form-label">
                    C.P.
                </label>

                <input
                    type="text"
                    name="postal_code"
                    value="{{ old('postal_code', $branch->postal_code ?? '') }}"
                    class="form-control">

            </div>

        </div>

    </div>


    {{-- Estado --}}
    <div class="mb-3">

        <label class="form-label">
            Estado de la sucursal
        </label>

        @php
        $currentStatus = old(
        'status',
        $branch->status ?? 'active'
        );
        @endphp

        <select
            name="status"
            class="form-select @error('status') is-invalid @enderror"
            required>

            <option
                value="active"
                @selected($currentStatus==='active' )>
                Activa
            </option>

            <option
                value="inactive"
                @selected($currentStatus==='inactive' )>
                Inactiva
            </option>

        </select>

        @error('status')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror


        @if($editing && $branch->is_main)

        <small class="form-hint text-warning">
            Esta es la sucursal principal.
            Para desactivarla primero debes establecer
            otra sucursal como principal.
        </small>

        @endif

    </div>

</div>