<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Insumo;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recetas = Receta::all();
        return view('recetas.index', compact('recetas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $insumos = Insumo::all();
        return view('recetas.create', compact('insumos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'indicaciones' => 'required|string',
            'tiempo_preparacion' => 'required|integer|min:1',
            'insumos' => 'required|array|min:1',
            'insumos.*.id' => 'required|exists:insumos,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ]);

        $receta = Receta::create([
            'nombre' => $request->nombre,
            'indicaciones' => $request->indicaciones,
            'tiempo_preparacion' => $request->tiempo_preparacion,
        ]);

        foreach ($request->insumos as $insumo) {
            $receta->insumos()->attach($insumo['id'], ['cantidad' => $insumo['cantidad']]);
        }

        return redirect()->route('recetas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receta $receta)
    {
        return view('recetas.show', compact('receta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Receta $receta)
    {
        $insumos = Insumo::all();
        return view('recetas.edit', compact('receta', 'insumos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receta $receta)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'indicaciones' => 'required|string',
            'tiempo_preparacion' => 'required|integer|min:1',
            'insumos' => 'required|array|min:1',
            'insumos.*.id' => 'required|exists:insumos,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ]);

        $receta->update([
            'nombre' => $request->nombre,
            'indicaciones' => $request->indicaciones,
            'tiempo_preparacion' => $request->tiempo_preparacion,
        ]);

        // Formatear los datos para sync
        $insumosSync = collect($request->insumos)->mapWithKeys(function ($insumo) {
            return [$insumo['id'] => ['cantidad' => $insumo['cantidad']]];
        })->all();

        // Actualizar los insumos usando sync
        $receta->insumos()->sync($insumosSync);

        return redirect()->route('recetas.index')->with('success', 'Receta actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receta $receta)
    {
        // Eliminar relaciones en la tabla pivote
        $receta->insumos()->detach();
        // Eliminar ventas relacionadas y sus movimientos
        foreach ($receta->ventas as $venta) {
            foreach ($venta->movimientos_inventario as $movimiento) {
                $movimiento->delete();
            }
            $venta->delete();
        }
        $receta->delete();
        return redirect()->route('recetas.index')->with('success', 'Receta eliminada correctamente');
    }
}
