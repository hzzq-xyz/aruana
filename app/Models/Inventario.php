<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Inventario extends Model
{
    use HasFactory, LogsActivity;
    
    protected $guarded = [];
    
    protected $casts = [
        'iluminado' => 'boolean',
        'impactos' => 'integer',
        'largura_px' => 'integer',
        'altura_px' => 'integer',
        'tempo_maximo' => 'integer',
        'qtd_slots' => 'integer',
    ];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()  // Loga TODOS os campos (já que usa $guarded = [])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at'])  // Ignora mudanças só do updated_at
            ->setDescriptionForEvent(fn(string $eventName) => "Inventário {$eventName}");
    }
    
    // --- RELACIONAMENTOS ---
    public function validacoes(): HasMany
    {
        return $this->hasMany(Validacao::class);
    }
}