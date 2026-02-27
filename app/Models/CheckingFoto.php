<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckingFoto extends Model
{
    protected $table = 'checking_fotos';

    protected $fillable = ['inventario_id', 'fotos', 'data_checking', 'observacoes'];

    protected $casts = [
        'fotos' => 'array', // Essencial para o upload múltiplo funcionar
        'data_checking' => 'date',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}