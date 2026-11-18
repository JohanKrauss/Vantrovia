<div class="card-body">

    <div class="mb-3">

        <label class="form-label">
            Nombre
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $unit->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Ej. Kilogramo"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="mb-3">

        <label class="form-label">
            Símbolo
        </label>

        <input
            type="text"
            name="symbol"
            value="{{ old('symbol', $unit->symbol ?? '') }}"
            class="form-control @error('symbol') is-invalid @enderror"
            placeholder="Ej. kg"
        >

        @error('symbol')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        <small class="form-hint">
            Ejemplos: pza, kg, g, L, ml, caja, bulto.
        </small>

    </div>

</div>