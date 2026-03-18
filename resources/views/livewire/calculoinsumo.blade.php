<?php

use Livewire\Volt\Component;
use App\Models\Receta;

new class extends Component {
    //
    public $count = 0;

    public $recetas = [];

    public $receta = null;

    public $cantidad = 0;

    public $insumos = [];

    public $resultado = '> Resultado:.....';

    public function mount()
    {
        $this->recetas = \App\Models\Receta::all();
    }

    public function increment()
    {
        $this->count++;
    }

    public function calcular()
    {
        $receta = Receta::find($this->receta);

        $this->insumos = $receta->insumos->map(function ($insumo) {
            return [
                'nombre' => $insumo->nombre,
                'cantidad' => $insumo->pivot->cantidad * $this->cantidad,
                'unidad_medida' => $insumo->unidad_medida->abreviatura,
                'imagen' => $insumo->imagen,
            ];
        });
    }
}; ?>

<div>
    <div class="card-styles">
        <div class="card-style-3 mb-30">
            <div class="card-content">
                <div class="alert-box primary-alert">
                    <div class="alert">
                        <p class="text-medium">
                            Calcular cantidad de insumos necesarios para produccion de [x] unidades de una receta
                            (plato).
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="select-style-1">
                            <label>¿Que receta quieres producir?</label>
                            <div class="select-position">
                                <select wire:model="receta">
                                    <option value="">Seleccione una receta</option>
                                    @foreach ($recetas as $recetita)
                                        <option value="{{ $recetita->id }}">{{ $recetita->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input-style-1">
                            <label>¿Que cantidad quieres producir?</label>
                            <input type="number" wire:model="cantidad" placeholder="Cantidad a producir">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="table-wrapper table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="lead-info">
                                            <h6>Insumo</h6>
                                        </th>
                                        <th class="lead-email">
                                            <h6>Cantidad Necesaria</h6>
                                        </th>
                                    </tr>
                                    <!-- end table row-->
                                </thead>
                                <tbody>
                                    @foreach ($insumos as $insumo)
                                        <tr>
                                            <td class="min-width">
                                                <div class="lead">
                                                    <div class="lead-image">
                                                        <img src="{{ $insumo['imagen'] ? asset('storage/' . $insumo - ['imagen']) : asset('images/recetas.jpg') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="lead-text">
                                                        <p>{{ $insumo['nombre'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="min-width">
                                                <p>{{ $insumo['cantidad'] }} {{ $insumo['unidad_medida'] }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- end table row -->
                                </tbody>
                            </table>
                            <!-- end table -->
                        </div>

                        <div class="button-group">
                            <button wire:click="calcular" class="main-btn primary-btn">Calcular</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
