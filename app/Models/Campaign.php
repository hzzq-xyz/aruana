<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'user_id', 'nome', 'data_inicio', 'data_fim', 
        'prioridade', 'peso_slot', 'status'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    // O Cliente
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Em quais painéis vai tocar?
    public function inventarios(): BelongsToMany
    {
        return $this->belongsToMany(Inventario::class, 'campaign_inventario');
    }

    // Quais VTs vão tocar?
    public function midias(): BelongsToMany
    {
        // Certifica-te de que o nome do teu modelo de VTs é ExternalMedia
        return $this->belongsToMany(ExternalMedia::class, 'campaign_external_media');
    }

    // Em que horários?
    public function schedules(): HasMany
    {
        return $this->hasMany(CampaignSchedule::class);
    }
}