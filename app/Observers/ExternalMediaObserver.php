<?php

namespace App\Observers;

use App\Models\ExternalMedia;
use App\Models\ConfiguracaoAlerta;
use App\Models\ConfiguracaoEmail;
use Illuminate\Support\Facades\Mail;

class ExternalMediaObserver
{
    /**
     * Dispara sempre que um novo upload é concluído no painel do cliente.
     */
    public function created(ExternalMedia $externalMedia): void
    {
        // 1. Busca as configurações dos dois módulos
        $configAlerta = ConfiguracaoAlerta::getConfig();
        $configVisual = ConfiguracaoEmail::getConfig();

        // 2. Só dispara se o alerta estiver ativado no painel de Config. Alertas
        if ($configAlerta && $configAlerta->ativar_alerta_admin && $configAlerta->email_recebimento_alerta) {
            
            // Carrega os dados do usuário e do painel de destino
            $externalMedia->load(['user', 'inventario']);

            $logoUrl = $configVisual->logo ? asset('storage/' . $configVisual->logo) : null;
            $corPrimaria = $configVisual->cor_primaria ?? '#2563eb';

            $html = "
                <div style='font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #1e293b; padding: 20px; text-align: center; color: white;'>
                        <h2 style='margin: 0;'>{$configAlerta->assunto_alerta_admin}</h2>
                    </div>
                    <div style='padding: 20px; line-height: 1.6;'>
                        " . ($logoUrl ? "<img src='{$logoUrl}' style='max-height: 40px; margin-bottom: 20px;'>" : "") . "
                        
                        <p>Olá, <strong>Gestor NELA</strong>!</p>
                        <p>Um novo criativo de mídia externa foi enviado e aguarda sua validação.</p>
                        
                        <div style='background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                            <p style='margin: 5px 0;'><strong>Cliente:</strong> {$externalMedia->client_name}</p>
                            <p style='margin: 5px 0;'><strong>Painel de Destino:</strong> " . ($externalMedia->inventario->codigo ?? 'Não identificado') . "</p>
                            <p style='margin: 5px 0;'><strong>Enviado por:</strong> " . ($externalMedia->user->name ?? 'Usuário do Sistema') . "</p>
                            <p style='margin: 5px 0;'><strong>Protocolo:</strong> <span style='color: {$corPrimaria}; font-weight: bold;'>{$externalMedia->protocol_id}</span></p>
                        </div>

                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='" . config('app.url') . "/admin/gerenciar-midias' 
                               style='background-color: {$corPrimaria}; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                               Analisar Material Agora
                            </a>
                        </div>
                    </div>
                    <div style='background-color: #f9fafb; padding: 10px; text-align: center; font-size: 12px; color: #9ca3af;'>
                        Este é um alerta automático do sistema de gestão de painéis NELA.
                    </div>
                </div>
            ";

            Mail::html($html, function ($message) use ($configAlerta, $externalMedia) {
                $message->to($configAlerta->email_recebimento_alerta)
                        ->subject($configAlerta->assunto_alerta_admin . " - " . $externalMedia->client_name);
            });
        }
    }
}