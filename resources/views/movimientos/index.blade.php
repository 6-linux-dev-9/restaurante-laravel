@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Movimientos de Inventario') }}
                        </span>
                        <div class="float-right">
                            <a href="{{ route('movimientos.create') }}" 
                               class="btn" 
                               style="background-color: #5A2828; color: white; border-radius: 5px; padding: 8px 20px;">
                                {{ __('CREAR NUEVO') }}
                            </a>
                        </div>
                    </div>
                </div>

                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>No</th>
                                    <th>Insumo</th>
                                    <th>Cantidad</th>
                                    <th>Tipo</th>
                                    <th>Motivo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movimientos as $movimiento)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $movimiento->insumo->nombre }}</td>
                                        <td>{{ $movimiento->cantidad }}</td>
                                        <td>
                                            <span class="badge {{ $movimiento->tipo == 'entrada' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $movimiento->tipo }}
                                            </span>
                                        </td>
                                        <td>{{ $movimiento->motivo }}</td>
                                        <td>{{ $movimiento->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <form action="{{ route('movimientos.destroy',$movimiento->id) }}" method="POST" style="display: inline-flex; gap: 8px;">
                                                <a class="btn" 
                                                   href="{{ route('movimientos.edit',$movimiento->id) }}"
                                                   style="background-color: #5A2828; color: white; border-radius: 5px; padding: 8px 20px;">
                                                    EDITAR
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn"
                                                        style="background-color: #5A2828; color: white; border-radius: 5px; padding: 8px 20px;">
                                                    ELIMINAR
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! $movimientos->links() !!}
        </div>
    </div>
</div>
@endsection 