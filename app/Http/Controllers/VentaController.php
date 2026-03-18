<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Receta;
use Illuminate\Http\Request;
use App\Models\MovimientoInventario;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::all();
        return view('ventas.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $recetas = Receta::all();
        return view('ventas.create', compact('recetas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'precio' => 'required|numeric',
            'total' => 'required|numeric',
            'receta_id' => 'required',
            'fecha' => 'required|date',
        ]);

        $venta = new Venta();
        $venta->cantidad = $request->cantidad;
        $venta->precio = $request->precio;
        $venta->total = $request->total;
        $venta->receta_id = $request->receta_id;
        $venta->created_at = $request->fecha;
        $venta->save();

        $receta = Receta::find($request->receta_id);
        $insumosGastados = $receta->insumosGastados($request->cantidad);

        foreach ($insumosGastados as $insumo_id => $cantidad) {
            $movimiento = new MovimientoInventario();
            $movimiento->tipo = 'salida';
            $movimiento->cantidad = $cantidad;
            $movimiento->insumo_id = $insumo_id;
            $movimiento->motivo = 'Venta de ' . $receta->nombre;
            $movimiento->venta_id = $venta->id;
            $movimiento->save();
        }

        return redirect()->route('ventas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        if (!auth()->user() || !auth()->user()->restaurante) {
            return redirect()->route('home')->with('error', 'No tienes un restaurante asociado.');
        }

        return view('ventas.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        $recetas = Receta::all();
        return view('ventas.edit', compact('venta', 'recetas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'precio' => 'required|numeric',
            'total' => 'required|numeric',
            'receta_id' => 'required|exists:recetas,id',
            'fecha' => 'required|date',
        ]);

        $venta->cantidad = $request->cantidad;
        $venta->precio = $request->precio;
        $venta->total = $request->total;
        $venta->receta_id = $request->receta_id;
        $venta->created_at = $request->fecha;
        $venta->save();
        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        // Eliminar movimientos relacionados
        foreach ($venta->movimientos_inventario as $movimiento) {
            $movimiento->delete();
        }
        $venta->delete();
        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente');
    }
}
