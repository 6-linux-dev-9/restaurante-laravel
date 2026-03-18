<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\MovimientoInventario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }
    public function vistaPrueba(){
        return view('reportes.prueba2');
    }

    public function getVentasData(Request $request)
    {
        $periodo = $request->get('periodo', 'semana');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        
        switch ($periodo) {
            case 'semana':
                $datos = $this->getVentasSemanales($fechaInicio, $fechaFin);
                break;
            case 'mes':
                $datos = $this->getVentasMensuales($fechaInicio, $fechaFin);
                break;
            case 'año':
                $datos = $this->getVentasAnuales($fechaInicio, $fechaFin);
                break;
            default:
                $datos = $this->getVentasSemanales($fechaInicio, $fechaFin);
        }

        return response()->json($datos);
    }

    private function getVentasSemanales($fechaInicio = null, $fechaFin = null)
    {
        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        } else {
            $fechaInicio = Carbon::now()->subDays(6)->startOfDay();
        }
        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        } else {
            $fechaFin = Carbon::now()->endOfDay();
        }
        // Obtener datos de ventas de los últimos 7 días o rango seleccionado
        $ventas = Venta::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('SUM(total) as total_ventas'),
            DB::raw('COUNT(*) as cantidad_ventas'),
            DB::raw('SUM(cantidad) as platos_vendidos')
        )
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        // Calcular estadísticas
        $totalVentas = $ventas->sum('cantidad_ventas');
        $totalIngresos = $ventas->sum('total_ventas');
        $totalPlatosVendidos = $ventas->sum('platos_vendidos');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;
        // Venta más alta y más baja (por ticket individual)
        $ventaMax = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('total')
            ->first();
        $ventaMin = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('total')
            ->first();
        return [
            'datos' => $ventas,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'ingresos_totales' => $totalIngresos,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $totalPlatosVendidos
            ],
            'venta_maxima' => $ventaMax ? [
                'plato' => $ventaMax->receta->nombre ?? 'N/A',
                'total' => $ventaMax->total
            ] : null,
            'venta_minima' => $ventaMin ? [
                'plato' => $ventaMin->receta->nombre ?? 'N/A',
                'total' => $ventaMin->total
            ] : null
        ];
    }

    private function getVentasMensuales($fechaInicio = null, $fechaFin = null)
    {
        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfMonth();
        } else {
            $fechaInicio = Carbon::now()->subMonths(11)->startOfMonth();
        }
        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfMonth();
        } else {
            $fechaFin = Carbon::now()->endOfMonth();
        }
        // Obtener datos de ventas
        $ventas = Venta::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as fecha'),
            DB::raw('SUM(total) as total_ventas'),
            DB::raw('COUNT(*) as cantidad_ventas'),
            DB::raw('SUM(cantidad) as platos_vendidos')
        )
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        // Calcular estadísticas
        $totalVentas = $ventas->sum('cantidad_ventas');
        $totalIngresos = $ventas->sum('total_ventas');
        $totalPlatosVendidos = $ventas->sum('platos_vendidos');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;
        // Venta más alta y más baja (por ticket individual)
        $ventaMax = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('total')
            ->first();
        $ventaMin = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('total')
            ->first();
        return [
            'datos' => $ventas,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'ingresos_totales' => $totalIngresos,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $totalPlatosVendidos
            ],
            'venta_maxima' => $ventaMax ? [
                'plato' => $ventaMax->receta->nombre ?? 'N/A',
                'total' => $ventaMax->total
            ] : null,
            'venta_minima' => $ventaMin ? [
                'plato' => $ventaMin->receta->nombre ?? 'N/A',
                'total' => $ventaMin->total
            ] : null
        ];
    }

    private function getVentasAnuales($fechaInicio = null, $fechaFin = null)
    {
        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfYear();
        } else {
            $fechaInicio = Carbon::now()->subYears(4)->startOfYear();
        }
        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfYear();
        } else {
            $fechaFin = Carbon::now()->endOfYear();
        }
        // Obtener datos de ventas
        $ventas = Venta::select(
            DB::raw('YEAR(created_at) as fecha'),
            DB::raw('SUM(total) as total_ventas'),
            DB::raw('COUNT(*) as cantidad_ventas'),
            DB::raw('SUM(cantidad) as platos_vendidos')
        )
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        // Calcular estadísticas
        $totalVentas = $ventas->sum('cantidad_ventas');
        $totalIngresos = $ventas->sum('total_ventas');
        $totalPlatosVendidos = $ventas->sum('platos_vendidos');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;
        // Venta más alta y más baja (por ticket individual)
        $ventaMax = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('total')
            ->first();
        $ventaMin = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('total')
            ->first();
        return [
            'datos' => $ventas,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'ingresos_totales' => $totalIngresos,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $totalPlatosVendidos
            ],
            'venta_maxima' => $ventaMax ? [
                'plato' => $ventaMax->receta->nombre ?? 'N/A',
                'total' => $ventaMax->total
            ] : null,
            'venta_minima' => $ventaMin ? [
                'plato' => $ventaMin->receta->nombre ?? 'N/A',
                'total' => $ventaMin->total
            ] : null
        ];
    }

    private function getEstadisticasSemanales()
    {
        $fechaInicio = Carbon::now()->subDays(6);
        
        // Estadísticas de ventas
        $ventasStats = Venta::where('created_at', '>=', $fechaInicio)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as total_ventas_monto,
                AVG(total) as promedio_venta
            ')->first();

        // Estadísticas de ingresos
        $ingresosStats = MovimientoInventario::where('created_at', '>=', $fechaInicio)
            ->where('tipo', 'entrada')
            ->count();

        return [
            'total_ventas' => $ventasStats->total_ventas ?? 0,
            'ingresos_totales' => $ingresosStats ?? 0,
            'promedio_venta' => $ventasStats->promedio_venta ?? 0
        ];
    }

    private function getEstadisticasMensuales()
    {
        $fechaInicio = Carbon::now()->startOfMonth();
        
        // Estadísticas de ventas
        $ventasStats = Venta::where('created_at', '>=', $fechaInicio)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as total_ventas_monto,
                AVG(total) as promedio_venta
            ')->first();

        // Estadísticas de ingresos
        $ingresosStats = MovimientoInventario::where('created_at', '>=', $fechaInicio)
            ->where('tipo', 'entrada')
            ->count();

        return [
            'total_ventas' => $ventasStats->total_ventas ?? 0,
            'ingresos_totales' => $ingresosStats ?? 0,
            'promedio_venta' => $ventasStats->promedio_venta ?? 0
        ];
    }

    private function getEstadisticasAnuales()
    {
        $fechaInicio = Carbon::now()->startOfYear();
        
        // Estadísticas de ventas
        $ventasStats = Venta::where('created_at', '>=', $fechaInicio)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as total_ventas_monto,
                AVG(total) as promedio_venta
            ')->first();

        // Estadísticas de ingresos
        $ingresosStats = MovimientoInventario::where('created_at', '>=', $fechaInicio)
            ->where('tipo', 'entrada')
            ->count();

        return [
            'total_ventas' => $ventasStats->total_ventas ?? 0,
            'ingresos_totales' => $ingresosStats ?? 0,
            'promedio_venta' => $ventasStats->promedio_venta ?? 0
        ];
    }

    public function parcialSemanal() {
        return view('reportes.parcial_semanal');
    }
    public function parcialMensual() {
        return view('reportes.parcial_mensual');
    }
    public function parcialAnual() {
        return view('reportes.parcial_anual');
    }


    //vista para reportes con multiples graficos - totalmente necesaria para renderizar todas las graficas paginadas
    public function vistaMultiple()
    {
        return view('reportes.vista-multiple');
    }
    
} 