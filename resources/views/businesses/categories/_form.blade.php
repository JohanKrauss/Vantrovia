<div class="card-body">

    <div class="mb-3">

        <label class="form-label">
            Nombre
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Ej. Alimentos"
            required>

        @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="mb-3">

        <label class="form-label">
            Descripción
        </label>

        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Descripción opcional de la categoría">{{ old('description', $category->description ?? '') }}</textarea>

        @error('description')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>

</div>