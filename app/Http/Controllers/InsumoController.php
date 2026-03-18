<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $insumos = Insumo::all();
        return view('insumos.index', compact('insumos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::all();
        $unidades = UnidadMedida::all();
        return view('insumos.create', compact('categorias', 'unidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:100',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'required',
            'unidad_medida_id' => 'required',
        ]);

        $insumo = Insumo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'stock_minimo' => $request->stock_minimo,
            'categoria_id' => $request->categoria_id,
            'unidad_medida_id' => $request->unidad_medida_id
        ]);

        return redirect()->route('insumos.index')->with('success', 'Insumo creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Insumo $insumo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insumo $insumo)
    {
        $categorias = Categoria::all();
        $unidades = UnidadMedida::all();
        return view('insumos.edit', compact('insumo', 'categorias', 'unidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Insumo $insumo)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:100',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_medida_id' => 'required|exists:unidad_medidas,id',
        ]);

        $data = $request->only(['nombre', 'descripcion', 'stock_minimo', 'categoria_id', 'unidad_medida_id']);

        $insumo->update($data);

        return redirect()->route('insumos.index')->with('success', 'Insumo actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insumo $insumo)
    {
        // Eliminar movimientos relacionados
        foreach ($insumo->movimiento_inventarios as $movimiento) {
            $movimiento->delete();
        }
        // Eliminar relaciones en recetas
        $insumo->recetas()->detach();
        if ($insumo->imagen) {
            Storage::disk('public')->delete($insumo->imagen);
        }
        $insumo->delete();
        return redirect()->route('insumos.index')->with('success', 'Insumo eliminado correctamente');
    }
}
