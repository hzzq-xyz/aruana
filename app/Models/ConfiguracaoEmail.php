<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoEmail extends Model
{
    protected $table = 'configuracoes_email';
    
    protected $fillable = [
        'logo',
        'cor_banner',
        'cor_primaria',
        'cor_secundaria',
        'texto_rodape_1',
        'texto_rodape_2',
        'nome_empresa',
        'telefone',
        'email_contato',
        'site',
        'endereco',
        'mostrar_logo',
        'mostrar_info_contato',
        'mensagem_adicional',
    ];
    
    protected $casts = [
        'mostrar_logo' => 'boolean',
        'mostrar_info_contato' => 'boolean',
    ];
    
    /**
     * Retorna a configuração ativa (singleton)
     */
    public static function getConfig()
    {
        return static::firstOrCreate([], [
            'nome_empresa' => 'NELA COMUNICAÇÃO',
            'cor_banner' => '#dc2626',
            'cor_primaria' => '#dc2626',
            'cor_secundaria' => '#666666',
            'texto_rodape_1' => 'Este é um informativo automático gerado pelo sistema NELA',
            'texto_rodape_2' => 'Para mais informações, entre em contato conosco',
            'mostrar_logo' => true,
            'mostrar_info_contato' => true,
        ]);
    }
}