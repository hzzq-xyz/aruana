<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ConfiguracaoAlerta extends Model
{
    use LogsActivity;
    
    // Define explicitamente o nome da tabela no banco de dados
    protected $table = 'configuracao_alertas';
    
    // Permite que o Filament salve estes campos
    protected $fillable = [
        'assunto_aprovacao',
        'mensagem_aprovacao',
        'assunto_reprovacao',
        'mensagem_reprovacao',
        'ativar_alerta_admin',
        'email_recebimento_alerta',
        'assunto_alerta_admin',
    ];
    
    // Garante que o toggle seja tratado como verdadeiro/falso pelo PHP
    protected $casts = [
        'ativar_alerta_admin' => 'boolean',
    ];
    
    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'assunto_aprovacao',
                'mensagem_aprovacao',
                'assunto_reprovacao',
                'mensagem_reprovacao',
                'ativar_alerta_admin',
                'email_recebimento_alerta',
                'assunto_alerta_admin',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Configuração de Alertas {$eventName}");
    }
    
    /**
     * Retorna a configuração ativa. 
     * Se não existir, cria uma com textos padrão para não dar erro no sistema.
     */
    public static function getConfig()
    {
        return static::firstOrCreate([], [
            'assunto_aprovacao' => '✅ Seu VT foi aprovado!',
            'mensagem_aprovacao' => 'Ótimas notícias! O seu material foi analisado e está pronto para ir para as ruas.',
            'assunto_reprovacao' => '⚠️ Seu VT precisa de ajustes',
            'mensagem_reprovacao' => 'O material enviado não atende aos padrões exigidos. Verifique as especificações técnicas e envie um novo arquivo.',
            'ativar_alerta_admin' => true,
            'assunto_alerta_admin' => '📢 NELA: Novo material aguardando aprovação',
        ]);
    }
}