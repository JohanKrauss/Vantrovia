@extends('layouts.app')

@section('title', 'Roles y permisos')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Roles y permisos
@endsection


@section('page-actions')

@section('page-actions')

@if(
auth()->user()->hasBusinessPermission(
$business,
'roles.create'
)
)

<a
    href="{{ route('businesses.roles.create', $business) }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nuevo rol
</a>

@endif

@endsection

@endsection


@section('content')

@if(session('success'))

<div class="alert alert-success alert-dismissible auto-dismiss-alert">

    <div>
        <i class="ti ti-circle-check me-2"></i>
        {{ session('success') }}
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"></button>

</div>

@endif


@if(session('warning'))

<div class="alert alert-warning alert-dismissible auto-dismiss-alert">

    <div>
        <i class="ti ti-alert-triangle me-2"></i>
        {{ session('warning') }}
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"></button>

</div>

@endif


<div class="card">

    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Rol</th>
                    <th>Permisos</th>
                    <th>Tipo</th>
                    <th class="w-1">
                        Acciones
                    </th>
                </tr>

            </thead>


            <tbody>

                @forelse($roles as $role)

                <tr>

                    <td>

                        <div class="fw-bold">
                            {{ $role->name }}
                        </div>

                        <div class="text-secondary small">
                            {{ $role->slug }}
                        </div>

                    </td>


                    <td>

                        <span class="badge bg-blue-lt">
                            {{ $role->permissions_count }}
                        </span>

                    </td>


                    <td>

                        @if($role->is_system)

                        <span class="badge bg-purple-lt">
                            Sistema
                        </span>

                        @else

                        <span class="badge bg-secondary-lt">
                            Personalizado
                        </span>

                        @endif

                    </td>


                    <td>

                        <div class="dropdown {{ $loop->last ? 'dropup' : '' }}">

                            <button
                                class="btn btn-icon"
                                data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>


                            <div class="dropdown-menu dropdown-menu-end">

                                @if(
                                auth()->user()->hasBusinessPermission(
                                $business,
                                'roles.update'
                                )
                                )

                                <a
                                    href="{{ route(
                                        'businesses.roles.edit',
                                        [
                                            $business,
                                            $role
                                        ]
                                    ) }}"
                                    class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>
                                    Editar
                                </a>

                                @endif


                                @if(
                                !$role->is_system &&
                                auth()->user()->hasBusinessPermission(
                                $business,
                                'roles.delete'
                                )
                                )

                                <div class="dropdown-divider"></div>

                                <form
                                    action="{{ route(
                                        'businesses.roles.destroy',
                                        [
                                            $business,
                                            $role
                                        ]
                                    ) }}"
                                    method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="dropdown-item text-danger">
                                        <i class="ti ti-trash me-2"></i>
                                        Eliminar
                                    </button>

                                </form>

                                @endif

                            </div>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="4"
                        class="text-center text-secondary py-5">
                        No hay roles configurados.
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection