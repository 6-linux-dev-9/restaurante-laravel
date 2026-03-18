@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Insumos') }}</h2>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="card-styles">
        <div class="card-style-3 mb-30">
            <div class="card-content">
                <div class="alert-box primary-alert">
                    <div class="alert">
                        <p class="text-medium">
                            Ingresa los datos del insumo
                        </p>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert-box danger-alert">
                        <div class="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-medium">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('insumos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-style-1">
                        <label>Nombre del insumo</label>
                        <input type="text" name="nombre" placeholder="Integresa el nombre">
                    </div>

                    <div class="input-style-1">
                        <label>Descripción del insumo</label>
                        <textarea placeholder="Descripción.." name="descripcion" rows="5"></textarea>
                    </div>

                    <!-- stock_minimum -->
                    <div class="input-style-1">
                        <label>Stock mínimo</label>
                        <input type="number" name="stock_minimo" placeholder="Integresa el stock mínimo" min="0">
                    </div>

                    <!-- categoria_id -->
                    <div class="select-style-1">
                        <label>Categoria</label>
                        <div class="select-position">
                            <select name="categoria_id">
                                <option value="">Selecciona una categoria</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- unidad_medida -->
                    <div class="select-style-1">
                        <label>Unidad de medida</label>
                        <div class="select-position">
                            <select name="unidad_medida_id">
                                <option value="">Selecciona una unidad de medida</option>
                                @foreach ($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Guardar Categoria</button>
                        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
