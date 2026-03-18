<?php

use Livewire\Volt\Component;
use App\Models\Receta;
use App\Models\Insumo;

new class extends Component {
    //
    public $recetas = [];

    public $receta = null;

    public $insumos = [];

    public $insumo = null;

    public $mes = null;
    public $resultado = '> Resultado:.....';

    public function mount()
    {
        /*if (auth()->user() && auth()->user()->restaurante) {
            $this->recetas = auth()->user()->restaurante->recetas;
        } else {
            $this->recetas = collect(); // Colección vacía si no hay restaurante
        }*/

        $this->recetas = Receta::all();
        $this->insumos = Insumo::all();
    }

    public function obtenerInsumos()
    {
        $receta = Receta::find($this->receta);

        $this->insumos = $receta->insumos;
    }

    public function predecir()
    {
        // hacer una solicitud a la API de predicción
        $nombreInsumo = Insumo::find($this->insumo)->nombre;
        $nombreReceta = Receta::find($this->receta)->nombre;

        $response = Http::post(env('HOST_MODELO_PREDICCION') . '/predict/desperdicio', [
            'insumo' => $nombreInsumo,
            'plato' => $nombreReceta,
            'mes' => $this->mes,
            'cantidad' => 10,
        ]);

        // Verificando la respuesta
        if ($response->successful()) {
            $data = $response->json(); // Convertir la respuesta en JSON
            $this->resultado = $data['desperdicio_predicho'] . ' ' . Insumo::find($this->insumo)->unidad_medida->abreviatura . ' de desperdicio'; // Mostrar el resultado
        } else {
            // Manejo de errores
            //dd('Error:', $response->status(), $response->body());
            $this->resultado = 'Error al predecir';
        }
    }
}; ?>

<div>
    <div class="card-styles">
        <div class="card-style-3 mb-30">
            <div class="card-content">
                <div class="alert-box primary-alert">
                    <div class="alert">
                        <p class="text-medium">
                            Predecir la cantidad de de desperdicio de [X] insumo en [Y] mes
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="select-style-1">
                            <label>¿Selecciona la receta que vas a producir?</label>
                            <div class="select-position">
                                <select wire:model="receta" wire:change="obtenerInsumos">
                                    <option value="">Seleccione una receta</option>
                                    @foreach ($recetas as $recetita)
                                        <option value="{{ $recetita->id }}">{{ $recetita->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="select-style-1">
                            <label>¿Qué insumo quieres predecir?</label>
                            <div class="select-position">
                                <select wire:model="insumo">
                                    <option value="">Seleccione un insumo</option>
                                    @foreach ($insumos as $ins)
                                        <option value="{{ $ins->id }}">{{ $ins->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="select-style-1">
                            <label>¿En que mes vas a producir?</label>
                            <div class="select-position">
                                <select wire:model="mes">
                                    <option value="">Seleccione un insumo</option>
                                    <option value="Enero">Enero</option>
                                    <option value="Febrero">Febrero</option>
                                    <option value="Marzo">Marzo</option>
                                    <option value="Abril">Abril</option>
                                    <option value="Mayo">Mayo</option>
                                    <option value="Junio">Junio</option>
                                    <option value="Julio">Julio</option>
                                    <option value="Agosto">Agosto</option>
                                    <option value="Septiembre">Septiembre</option>
                                    <option value="Octubre">Octubre</option>
                                    <option value="Noviembre">Noviembre</option>
                                    <option value="Diciembre">Diciembre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-style-1">
                            <label>Resultado</label>
                            <textarea wire:model="resultado" rows="5" readonly></textarea>
                        </div>

                        <div class="button-group" style="text-align: center;">
                            <button wire:click="predecir" class="main-btn active-btn btn-hover"><i
                                    class="lni lni-android"></i> Predecir</button>
                            <button wire:click="reentrenar" class="main-btn dark-btn btn-hover"><i
                                    class="lni lni-cog"></i> Re-entrenar</button>
                        </div>

                        <p style="text-align: center;" class="mt-3">Ultimo entreno: 05/02/2025</p>

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
