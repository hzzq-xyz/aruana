<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Validacao extends Model
{
    use LogsActivity;
    
    // Define o nome da tabela caso o Laravel não identifique automaticamente
    protected $table = 'validacoes';
    
    // Permite a gravação em massa (Mass Assignment)
    protected $guarded = [];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()  // Loga TODOS os campos (já que usa $guarded = [])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Validação {$eventName}");
    }
    
    /**
     * Relacionamento: Uma validação pertence a um item do inventário.
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }
}