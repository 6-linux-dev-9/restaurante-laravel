<?php

use Livewire\Volt\Component;
use App\Models\Receta;
use App\Models\Insumo;
use Illuminate\Support\Facades\Http;

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
        $this->recetas = \App\Models\Receta::all();
        $this->insumos = \App\Models\Insumo::all();
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

        $response = Http::post(env('HOST_MODELO_PREDICCION') . '/predict/month_plato', [
            'insumo' => $nombreInsumo,
            'plato' => $nombreReceta,
            'mes' => $this->mes,
            'desperdicio' => 0.1,
        ]);

        // Verificando la respuesta
        if ($response->successful()) {
            $data = $response->json(); // Convertir la respuesta en JSON
            $this->resultado = $data['cantidad_predicha'] . ' ' .Insumo::find($this->insumo)->unidad_medida->abreviatura; // Mostrar el resultado
        } else {
            // Manejo de errores
            //dd('Error:', $response->status(), $response->body());
            $this->resultado = 'Error al predecir';
        }
    }

    public function ensamblarData()
    {
        $this->resultado = 'Ensamblado...';

        // necesito crear un archivo data.csv
        $filePath = 'exports/data.csv';
        $file = fopen($filePath, 'w');

        // la cabecera es INSUMO,CANTIDAD,DÍA,MES,PLATO,DESPERDICIO
        $cabecera = ['INSUMO', 'CANTIDAD', 'DÍA', 'MES', 'PLATO', 'DESPERDICIO'];

        // Obtener movimientos de los usuarios
        $movimientos = auth()->user()->restaurante->movimientos;
        // Filtrar movimientos de tipo "salida"
        $movimientosSalida = $movimientos->where('tipo', 'salida');

        // Escribir la cabecera en el archivo CSV
        fputcsv($file, $cabecera);

        // Recorrer los movimientos de salida y escribir cada uno en el archivo CSV
        foreach ($movimientosSalida as $movimiento) {
            // obtener dia y mes
            $dia = $movimiento->created_at->format('d');
            $mes = $movimiento->created_at->format('m');
            $diaSemana = $movimiento->created_at->format('l'); // Obtener el día de la semana en inglés
            $mesNombre = $movimiento->created_at->format('F'); // Obtener el nombre del mes en inglés

            // Traducir el día de la semana al español
            $diasSemana = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo',
            ];
            $diaSemana = $diasSemana[$diaSemana];

            // Traducir el nombre del mes al español
            $meses = [
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre',
            ];
            $mesNombre = $meses[$mesNombre];
            if ($movimiento->venta && $movimiento->venta->receta) {
                $nombreReceta = $movimiento->venta->receta->nombre;

                $desperdicio = 0.1;
                foreach ($movimiento->venta->receta->insumos as $insumo) {
                    if ($insumo->id == $movimiento->insumo->id) {
                        $desperdicio = $insumo->pivot->desperdicio;
                        break;
                    }
                }

                // si desperdicio es nulo, entonces = 0.1
                if ($desperdicio == null) {
                    $desperdicio = 0.1;
                }

                $fila = [$movimiento->insumo->nombre, $movimiento->cantidad, $diaSemana, $mesNombre, $nombreReceta, $desperdicio * $movimiento->cantidad];
                fputcsv($file, $fila);
            }
        }

        // Cerrar el archivo CSV
        fclose($file);

        // Retornar la URL del archivo CSV
        return $filePath;
    }

    public function reentrenar()
    {
        $archivo = public_path($this->ensamblarData());

        // haremos una solicitud a la API de reentrenamiento http://127.0.0.1:5000/retrain
        $response = Http::attach('data_file', file_get_contents($archivo), 'data.csv')->post(env('HOST_MODELO_PREDICCION') . '/retrain');

        // Verificando la respuesta
        if ($response->successful()) {
            $this->resultado = 'Re-entrenamiento exitoso: ' . $response->body();
        } else {
            $this->resultado = 'Error al re-entrenar: ' . $response->body();
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
                            Predecir la cantidad de insumos que se necesitarán para producir [x] receta en [y] mes.
                        </p>
                    </div>
                </div>

                <div class="row">

                    <div class="col-6">
                        <div class="select-style-1">
                            <label>¿Que receta quieres producir?</label>
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
