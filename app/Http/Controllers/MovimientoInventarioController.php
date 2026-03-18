<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Insumo;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockBajo;

class MovimientoInventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movimientos = MovimientoInventario::paginate();
        return view('movimientos.index', compact('movimientos'))
            ->with('i', (request()->input('page', 1) - 1) * $movimientos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $insumos = Insumo::all();
        return view('movimientos.create', compact('insumos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'tipo' => 'required|in:entrada,salida',
            'motivo' => 'required|string|max:100',
            'insumo_id' => 'required|exists:insumos,id',
            'fecha' => 'required|date',
        ]);

        // Validación para salidas: no permitir más que el stock disponible
        if ($request->tipo === 'salida') {
            $insumo = Insumo::find($request->insumo_id);
            $stockDisponible = $insumo->getCantidadTotal();
            if ($request->cantidad > $stockDisponible) {
                return back()->withErrors(['cantidad' => 'No hay suficiente stock disponible para realizar esta salida. Stock actual: ' . $stockDisponible]);
            }
        }

        // Si es salida y ya tocó el stock mínimo, enviar notificación
        if ($request->tipo === 'salida') {
            $insumo = Insumo::find($request->insumo_id);
            if ($insumo->cantidad <= $insumo->stock_minimo) {
                // Obtener usaurios con rol admin
                $usuariosAdmin = User::role('admin')->get();
                // Enviar notificación a los usuarios administradores
                //$usuariosAdmin->notify(new StockBajo($insumo));
                Notification::send($usuariosAdmin, new StockBajo($insumo));
            }
        }

        // Crear el movimiento de inventario
        MovimientoInventario::create([
            'cantidad' => $request->cantidad,
            'tipo' => $request->tipo,
            'motivo' => $request->motivo,
            'insumo_id' => $request->insumo_id,
            'created_at' => $request->fecha,
        ]);

        return redirect()->route('movimientos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(MovimientoInventario $movimiento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MovimientoInventario $movimiento)
    {
        $insumos = \App\Models\Insumo::all();
        return view('movimientos.edit', compact('movimiento', 'insumos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MovimientoInventario $movimiento)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'tipo' => 'required|in:entrada,salida',
            'motivo' => 'required|string|max:100',
            'insumo_id' => 'required|exists:insumos,id',
            'fecha' => 'required|date',
        ]);

        $movimiento->cantidad = $request->cantidad;
        $movimiento->tipo = $request->tipo;
        $movimiento->motivo = $request->motivo;
        $movimiento->insumo_id = $request->insumo_id;
        $movimiento->created_at = $request->fecha;
        $movimiento->save();

        return redirect()->route('movimientos.index')->with('success', 'Movimiento actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MovimientoInventario $movimiento)
    {
        $movimiento->delete();
        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado correctamente');
    }
}
