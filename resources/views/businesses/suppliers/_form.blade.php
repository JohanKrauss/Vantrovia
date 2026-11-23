<div class="card-body">

    <div class="row">

        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Nombre comercial
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $supplier->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Ej. Distribuidora López"
                    required>

                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Razón social
                </label>

                <input
                    type="text"
                    name="legal_name"
                    value="{{ old('legal_name', $supplier->legal_name ?? '') }}"
                    class="form-control @error('legal_name') is-invalid @enderror"
                    placeholder="Ej. Distribuidora López S.A. de C.V.">

                @error('legal_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-md-4">

            <div class="mb-3">

                <label class="form-label">
                    RFC / Identificador fiscal
                </label>

                <input
                    type="text"
                    name="tax_id"
                    value="{{ old('tax_id', $supplier->tax_id ?? '') }}"
                    class="form-control @error('tax_id') is-invalid @enderror"
                    placeholder="Ej. XAXX010101000">

                @error('tax_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        <div class="col-md-4">

            <div class="mb-3">

                <label class="form-label">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $supplier->email ?? '') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="ventas@proveedor.com">

                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

        </div>


        <div class="col-md-4">

            <div class="mb-3">

                <label class="form-label">
                    Teléfono
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $supplier->phone ?? '') }}"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="Ej. 55 1234 5678">

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
            Dirección
        </label>

        <textarea
            name="address"
            rows="3"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="Dirección del proveedor">{{ old('address', $supplier->address ?? '') }}</textarea>

        @error('address')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="mb-0">

        <label class="form-label">
            Notas
        </label>

        <textarea
            name="notes"
            rows="3"
            class="form-control @error('notes') is-invalid @enderror"
            placeholder="Información adicional...">{{ old('notes', $supplier->notes ?? '') }}</textarea>

        @error('notes')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>

</div>