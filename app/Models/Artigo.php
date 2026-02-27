<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Artigo extends Model
{
    use LogsActivity;

    protected $fillable = [
        'titulo', 
        'slug', 
        'categoria', 
        'conteudo', 
        'pdf_path', // ADICIONADO: Necessário para o upload do PDF funcionar
        'is_ativo', 
        'ordem'
    ];

    /**
     * Casts para garantir que os tipos de dados venham corretos do banco
     */
    protected $casts = [
        'is_ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /**
     * Gera o slug automaticamente ao salvar o título
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Sempre gera o slug se o título mudar ou se estiver vazio
            if (empty($model->slug) || $model->isDirty('titulo')) {
                $model->slug = Str::slug($model->titulo);
            }
        });
    }

    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Documento '{$this->titulo}' foi {$eventName}");
    }
}