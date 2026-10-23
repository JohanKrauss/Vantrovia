@php
$editing = isset($business);
@endphp

<div class="card-body">

    <div class="mb-3">

        <label class="form-label">
            Nombre comercial
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $business->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Ej. Mascota Acuario Mini"
            required>

        @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="mb-3">

        <label class="form-label">
            Subdominio
        </label>

        <div class="input-group">

            <input
                type="text"
                name="slug"
                value="{{ old('slug', $business->slug ?? '') }}"
                class="form-control @error('slug') is-invalid @enderror"
                placeholder="mascota-acuario"
                {{ $editing ? 'required' : '' }}>

            <span class="input-group-text">
                .vantrovia.com
            </span>

        </div>

        @error('slug')
        <div class="text-danger small mt-1">
            {{ $message }}
        </div>
        @enderror

        @if(!$editing)
        <small class="form-hint">
            Si lo dejas vacío, Vantrovia lo generará automáticamente.
        </small>
        @else
        <small class="form-hint">
            Cambiar este valor modificará posteriormente el subdominio del negocio.
        </small>
        @endif

    </div>


    <div class="mb-3">

        <label class="form-label">
            Razón social
        </label>

        <input
            type="text"
            name="legal_name"
            value="{{ old('legal_name', $business->legal_name ?? '') }}"
            class="form-control @error('legal_name') is-invalid @enderror"
            placeholder="Opcional">

        @error('legal_name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="row">

        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Correo
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $business->email ?? '') }}"
                    class="form-control @error('email') is-invalid @enderror">

                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Teléfono
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $business->phone ?? '') }}"
                    class="form-control @error('phone') is-invalid @enderror">

                @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Estado
        </label>

        <select
            name="status"
            class="form-select @error('status') is-invalid @enderror"
            required>

            @php
            $currentStatus = old(
            'status',
            $business->status ?? 'active'
            );
            @endphp

            <option
                value="active"
                @selected($currentStatus==='active' )>
                Activo
            </option>

            <option
                value="pending"
                @selected($currentStatus==='pending' )>
                Pendiente
            </option>

            <option
                value="suspended"
                @selected($currentStatus==='suspended' )>
                Suspendido
            </option>

            <option
                value="cancelled"
                @selected($currentStatus==='cancelled' )>
                Cancelado
            </option>

        </select>

    </div>

</div>