<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;  // ✅ Import no topo
use Spatie\Activitylog\LogOptions;  // ✅ Import no topo

class Atendimento extends Model
{
    use LogsActivity;  // ✅ Trait logo após "class", antes das properties
    
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'ativo',
    ];
    
    protected $casts = [
        'ativo' => 'boolean',
    ];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nome', 'email', 'telefone', 'ativo'])  // ✅ Campos do Atendimento
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Atendimento {$eventName}");
    }
    
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