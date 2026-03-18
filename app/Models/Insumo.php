<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    //

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock_minimo',
        'imagen',
        'categoria_id',
        'restaurante_id',
        'unidad_medida_id',
    ];

    public function unidad_medida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function recetas()
    {
        return $this->belongsToMany(Receta::class, 'recetas_insumos')
            ->withPivot('cantidad', 'desperdicio') // Incluye los campos extra
            ->withTimestamps();    // Incluye marcas de tiempo
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimiento_inventarios()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Función para obtener la cantidad total del insumo en el restaurante, sumando las cantidades de los movimientos de inventario de tipo entrada y restando las de tipo salida
    public function getCantidadTotal()
    {
        $entradas = $this->movimiento_inventarios->where('tipo', 'entrada')->sum('cantidad');
        $salidas = $this->movimiento_inventarios->where('tipo', 'salida')->sum('cantidad');
        return $entradas - $salidas;
    }
}
