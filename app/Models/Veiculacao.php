<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Veiculacao extends Model
{
    use LogsActivity;
    
    protected $table = 'veiculacoes';
    
    protected $fillable = [
        'inventario_id',
        'cliente',
        'email_cliente',
        'atendimento',
        'data_inicio',
        'data_fim',
        'slots',
        'tipo_acordo',
        'observacoes',
        'imagem_campanha',
    ];
    
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'slots' => 'integer',
    ];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'inventario_id',
                'cliente',
                'email_cliente',
                'atendimento',
                'data_inicio',
                'data_fim',
                'slots',
                'tipo_acordo',
                'observacoes',
            ])  // Não loga 'imagem_campanha' (caminho de arquivo)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Veiculação {$eventName}");
    }
    
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }
}