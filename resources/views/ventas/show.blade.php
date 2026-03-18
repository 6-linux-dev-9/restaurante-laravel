@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Detalle de Ventas') }}</h2>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="card-style settings-card-1 mb-30 mx-auto" style="max-width: 800px;">
        <div class="title mb-30 d-flex justify-content-between align-items-center">
            <h6>Venta: {{ $venta->id }}</h6>
            <button class="border-0 bg-transparent">
                <i class="lni lni-pencil-alt"></i>
            </button>
        </div>
        <div class="profile-info">
            <div class="d-flex align-items-center mb-30">
                <div class="profile-image">
                    <img src="{{ $venta->receta->imagen ? asset('storage/' . $venta->receta->imagen) : asset('images/recetas.jpg') }}"
                        alt="" width="80" height="80">
                </div>
                <div class="profile-meta">
                    <h2 class="text-bold text-dark mb-10"> {{ $venta->receta->nombre }}</h2>
                    <p> Fecha: {{ $venta->created_at }}</p>
                </div>
            </div>
            <div class="input-style-1">
                <label>Cantidad Vendida</label>
                <input type="text" value="{{ $venta->cantidad }}" readonly>
            </div>
            <div class="input-style-1">
                <label>Precio por porción</label>
                <input type="text" value="{{ $venta->precio }} Bs." readonly>
            </div>
            <div class="input-style-1">
                <label>Total</label>
                <input type="text" value="{{ $venta->total }} Bs." readonly>
            </div>

            <div class="input-style-1">
                <label>Insumos</label>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->receta->insumos as $insumo)
                            <tr>
                                <td>
                                    <div class="lead">
                                        <div class="lead-image">
                                            <img src="{{ $insumo->imagen ? asset('storage/' . $insumo->imagen) : asset('images/cereales.jpg') }}"
                                                alt="">
                                        </div>
                                        <div class="lead-text">
                                            <p>{{ $insumo->nombre }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $insumo->pivot->cantidad * $venta->cantidad }}</td>
                                <td>{{ $insumo->unidad_medida->abreviatura }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
