<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ExternalMedia extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'inventario_id',
        'file_path',
        'client_name',
        'pi_number',
        'start_date',
        'end_date',
        'feedback',
        'user_id',
        'protocol_id',
        'status',
    ];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'inventario_id',
                'client_name',
                'pi_number',
                'start_date',
                'end_date',
                'status',
                'feedback',
            ])  // Não loga 'file_path' (caminho de arquivo)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Mídia Externa {$eventName}");
    }
    
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}