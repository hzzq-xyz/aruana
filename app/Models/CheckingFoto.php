<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CheckingFoto extends Model
{
    use LogsActivity;
    
    protected $table = 'checking_fotos';
    
    protected $fillable = [
        'inventario_id',
        'fotos',
        'data_checking',
        'observacoes'
    ];
    
    protected $casts = [
        'fotos' => 'array', // Essencial para o upload múltiplo funcionar
        'data_checking' => 'date',
    ];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['inventario_id', 'data_checking', 'observacoes'])  // Não loga 'fotos' (muito pesado)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Checking de Foto {$eventName}");
    }
    
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}