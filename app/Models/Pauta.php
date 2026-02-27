<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pauta extends Model
{
    use LogsActivity;
    
    protected $guarded = []; // Libera todos os campos para escrita
    
    protected $casts = [
        'data_insercao' => 'date',
        'prazo_captacao' => 'date',
        'prazo_envio' => 'date',
        'data_captacao' => 'date',
        'data_envio_real' => 'date',
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
            ->setDescriptionForEvent(fn(string $eventName) => "Pauta {$eventName}");
    }
    
    // Relacionamento principal (um painel específico)
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }
    
    // Relacionamento múltiplo (vários painéis numa pauta)
    public function inventarios(): BelongsToMany
    {
        return $this->belongsToMany(Inventario::class, 'inventario_pauta');
    }
}