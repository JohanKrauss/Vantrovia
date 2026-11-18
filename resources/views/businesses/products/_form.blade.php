<div class="card-body">

    <div class="row">

        <div class="col-md-8">

            <div class="mb-3">

                <label class="form-label">
                    Nombre del producto
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $product->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Ej. Croquetas Nupec Adulto 20 kg"
                    required
                >

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>


        <div class="col-md-4">

            <div class="mb-3">

                <label class="form-label">
                    SKU
                </label>

                <input
                    type="text"
                    name="sku"
                    value="{{ old('sku', $product->sku ?? '') }}"
                    class="form-control @error('sku') is-invalid @enderror"
                    placeholder="Ej. NUP-AD-20"
                >

                @error('sku')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Categoría
                </label>

                <select
                    name="category_id"
                    class="form-select @error('category_id') is-invalid @enderror"
                >

                    <option value="">
                        Sin categoría
                    </option>

                    @foreach($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected(
                                old(
                                    'category_id',
                                    $product->category_id ?? null
                                ) == $category->id
                            )
                        >
                            {{ $category->name }}

                            @if(!$category->is_active)
                                (Inactiva)
                            @endif
                        </option>

                    @endforeach

                </select>

                @error('category_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>


        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Unidad de medida
                </label>

                <select
                    name="unit_id"
                    class="form-select @error('unit_id') is-invalid @enderror"
                >

                    <option value="">
                        Sin unidad
                    </option>

                    @foreach($units as $unit)

                        <option
                            value="{{ $unit->id }}"
                            @selected(
                                old(
                                    'unit_id',
                                    $product->unit_id ?? null
                                ) == $unit->id
                            )
                        >
                            {{ $unit->name }}

                            @if($unit->symbol)
                                ({{ $unit->symbol }})
                            @endif

                            @if(!$unit->is_active)
                                - Inactiva
                            @endif
                        </option>

                    @endforeach

                </select>

                @error('unit_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Código de barras
        </label>

        <input
            type="text"
            name="barcode"
            value="{{ old('barcode', $product->barcode ?? '') }}"
            class="form-control @error('barcode') is-invalid @enderror"
            placeholder="Ej. 7501234567890"
        >

        @error('barcode')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="row">

        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Costo
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        $
                    </span>

                    <input
                        type="number"
                        name="cost"
                        value="{{ old('cost', $product->cost ?? '0.00') }}"
                        class="form-control @error('cost') is-invalid @enderror"
                        min="0"
                        step="0.01"
                        required
                    >

                    @error('cost')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>


        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Precio de venta
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        $
                    </span>

                    <input
                        type="number"
                        name="sale_price"
                        value="{{ old('sale_price', $product->sale_price ?? '0.00') }}"
                        class="form-control @error('sale_price') is-invalid @enderror"
                        min="0"
                        step="0.01"
                        required
                    >

                    @error('sale_price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Descripción
        </label>

        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Información adicional del producto"
        >{{ old('description', $product->description ?? '') }}</textarea>

        @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>