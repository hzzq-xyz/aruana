<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSchedule extends Model
{
    protected $fillable = ['campaign_id', 'dia_semana', 'hora_inicio', 'hora_fim'];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}