<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    protected $fillable = [
        'nome',
        'ativo',
    ];
    
    protected $casts = [
        'ativo' => 'boolean',
    ];
    
    /**
     * Retorna apenas atendimentos ativos
     */
    public static function ativos()
    {
        return static::where('ativo', true)->orderBy('nome')->get();
    }
    
    /**
     * Retorna options para select
     */
    public static function options()
    {
        return static::ativos()->pluck('nome', 'nome')->toArray();
    }
}
