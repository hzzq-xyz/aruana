<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalMedia extends Model
{
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
        'status', // AQUI ESTÁ A CORREÇÃO! Agora o Laravel permite alterar o status.
    ];

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}