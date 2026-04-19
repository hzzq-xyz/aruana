<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayLog extends Model
{
    protected $fillable = ['inventario_id', 'external_media_id', 'campaign_id', 'played_at'];
    
    // Desativa o created_at/updated_at padrão pois já usamos o played_at
    public $timestamps = false; 

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function inventario(): BelongsTo { return $this->belongsTo(Inventario::class); }
    public function media(): BelongsTo { return $this->belongsTo(ExternalMedia::class, 'external_media_id'); }
}