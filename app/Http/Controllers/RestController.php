<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestController extends Controller
{
    

    public function obtenerVentasAgrupadas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $granularidad = $request->input('granularidad');
        $intervalo = $request->input('intervalo');
        $nombreReceta = $request->input('nombre_receta'); 

        $joinReceta = '';
        $condicionReceta = '';

        $params = [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'intervalo' => $intervalo,
        ];
        
        if (!empty($nombreReceta)) {
            $nombreNormalizado = strtolower($nombreReceta);
            $palabrasClave = ['ventas', 'venta'];
            if (!in_array($nombreNormalizado, $palabrasClave)) {
                $joinReceta = "INNER JOIN recetas r ON r.id = v.receta_id";
                //si usas like es mas lento pero soporta indiferente del nombre del producto, ojo si colocas en platillos o receta 2 pizzas entonces de preferencia normalizar el nombre en la bd
                $condicionReceta = "AND r.nombre ILIKE '%' || :nombre_receta::text || '%'";
                
                //pizza cacera
                //pizza napolitana

                //si lo normalizas la bd  a solo minuscula sin acento o instalando un funcion de pg para que reconozca independiente del acento y demas en (postgre)
                //$condicionReceta = "AND r.nombre = :nombre_receta";
                // Añadimos el parámetro solo si se va a usar
                $params['nombre_receta'] = $nombreReceta;
            }
        
        }
        if($granularidad == "custom"){
            $sql = "
                WITH series AS (
                    SELECT generate_series(
                        :fecha_inicio::timestamp,
                        :fecha_fin::timestamp,
                        :intervalo::interval
                    ) AS periodo
                ),
                datos AS (
                    SELECT
                        (
                            SELECT s.periodo 
                            FROM series s
                            WHERE s.periodo <= v.created_at
                            ORDER BY s.periodo DESC
                            LIMIT 1
                        ) AS periodo_agrupado,
                        SUM(v.total) AS total_ventas,
                        SUM(v.cantidad) AS platos_vendidos
                    FROM ventas v
                    $joinReceta
                    WHERE v.created_at BETWEEN :fecha_inicio AND :fecha_fin
                    $condicionReceta
                    GROUP BY periodo_agrupado
                )
                SELECT 
                    TO_CHAR(series.periodo, 'YYYY-MM-DD HH24:MI') AS etiqueta,
                    COALESCE(datos.total_ventas, 0) AS total_ventas,
                    COALESCE(datos.platos_vendidos, 0) AS platos_vendidos
                FROM series
                LEFT JOIN datos ON series.periodo = datos.periodo_agrupado
                ORDER BY series.periodo;
            ";
        }else{
            $params['granularidad'] = $granularidad;
            
            $sql = "
                WITH series AS (
                    SELECT generate_series(
                        :fecha_inicio::timestamp,
                        :fecha_fin::timestamp,
                        :intervalo::interval
                    ) AS periodo
                ),
                datos AS (
                    SELECT 
                        date_trunc(:granularidad, v.created_at) AS periodo,
                        SUM(v.total) AS total_ventas,
                        SUM(v.cantidad) AS platos_vendidos
                    FROM ventas v
                    $joinReceta
                    WHERE v.created_at BETWEEN :fecha_inicio AND :fecha_fin
                    $condicionReceta
                    GROUP BY periodo
                )
                SELECT 
                    TO_CHAR(series.periodo, 'YYYY-MM-DD HH24:MI') AS etiqueta,
                    COALESCE(datos.total_ventas, 0) AS total_ventas,
                    COALESCE(datos.platos_vendidos, 0) AS platos_vendidos
                FROM series
                LEFT JOIN datos ON date_trunc(:granularidad, series.periodo) = datos.periodo
                ORDER BY series.periodo;
            ";
        }
        
        $resultados = DB::select($sql, $params);

        $coleccion = collect($resultados)->map(function ($item) {
            $item->total_ventas = (int) $item->total_ventas;
            $item->platos_vendidos = (int) $item->platos_vendidos;
            return $item;
        });
        $ingresosTotales = $coleccion->sum('total_ventas');
        $platosVendidos = $coleccion->sum('platos_vendidos');
        $promedioVenta = $platosVendidos > 0 ? round($ingresosTotales / $platosVendidos, 2) : 0;

        return response()->json([
            'data' => $resultados,
            'resumen' => [
                'ingresos_totales' => $ingresosTotales,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $platosVendidos,
            ]
        ]);
    }

}
