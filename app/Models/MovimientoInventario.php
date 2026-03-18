<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    //

    protected $fillable = [
        'cantidad',
        'tipo',
        'motivo',
        'insumo_id',
        'restaurante_id',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
