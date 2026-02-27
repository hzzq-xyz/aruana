@php
    $config = \App\Models\ConfiguracaoEmail::getConfig();
    
    // Agrupar veiculações por canal
    $veiculacoesPorCanal = $veiculacoes->groupBy(function($v) {
        return $v->inventario ? $v->inventario->canal : 'Sem Canal';
    });
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informativo de Veiculação</title>
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <div style="max-width: 900px; margin: 0 auto; background-color: white; border: 2px solid #e0e0e0;">
        
        {{-- Header com Logo --}}
        <div style="padding: 20px; border-bottom: 2px solid #e0e0e0;">
            <div style="display: flex; align-items: center; gap: 15px;">
                @if($config->mostrar_logo && $config->logo)
                    <img src="{{ config('app.url') . Storage::url($config->logo) }}" 
                         alt="{{ $config->nome_empresa }}" 
                         style="max-height: 60px; width: auto;">
                @else
                    <div style="background-color: {{ $config->cor_primaria }}; width: 60px; height: 60px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                            <text x="12" y="17" text-anchor="middle" fill="white" font-size="14" font-weight="bold">i</text>
                        </svg>
                    </div>
                @endif
                
                <div>
                    <div style="color: {{ $config->cor_primaria }}; font-size: 18px; font-weight: bold; margin: 0;">Informativo</div>
                    <div style="color: {{ $config->cor_primaria }}; font-size: 18px; font-weight: bold; margin: 0;">de veiculação</div>
                </div>
            </div>
        </div>

        {{-- Banner --}}
        <div style="background-color: {{ $config->cor_banner }}; color: white; text-align: center; padding: 12px; font-size: 20px; font-weight: bold; letter-spacing: 2px;">
            INFORMATIVO DIGITAL
        </div>

        {{-- Tabela de Informações (COMPACTA) --}}
        <table style="width: 100%; border-collapse: collapse; margin: 0;">
            <tr style="background-color: #f3f4f6;">
                <td style="padding: 8px 15px; font-weight: bold; text-align: right; width: 30%; border: 1px solid #e0e0e0; font-size: 13px;">
                    CLIENTE
                </td>
                <td style="padding: 8px 15px; border: 1px solid #e0e0e0; font-weight: bold; text-transform: uppercase; font-size: 13px;">
                    {{ $nomeCliente }}
                </td>
            </tr>

            <tr>
                <td style="padding: 8px 15px; font-weight: bold; text-align: right; background-color: #f3f4f6; border: 1px solid #e0e0e0; font-size: 13px;">
                    CAMPANHA
                </td>
                <td style="padding: 8px 15px; border: 1px solid #e0e0e0; font-weight: bold; text-transform: uppercase; font-size: 13px;">
                    {{ $nomeCampanha }}
                </td>
            </tr>

            <tr style="background-color: #f3f4f6;">
                <td style="padding: 8px 15px; font-weight: bold; text-align: right; border: 1px solid #e0e0e0; font-size: 13px;">
                    ATENDIMENTO
                </td>
                <td style="padding: 8px 15px; border: 1px solid #e0e0e0; font-weight: bold; text-transform: uppercase; font-size: 13px;">
                    {{ $atendimento }}
                </td>
            </tr>

            {{-- PLATAFORMAS AGRUPADAS POR CANAL --}}
            <tr>
                <td style="padding: 8px 15px; font-weight: bold; text-align: right; background-color: #f3f4f6; border: 1px solid #e0e0e0; vertical-align: top; font-size: 13px;">
                    PLATAFORMA{{ $veiculacoesPorCanal->count() > 1 ? 'S' : '' }}
                </td>
                <td style="padding: 8px 15px; border: 1px solid #e0e0e0; font-size: 13px;">
                    @foreach($veiculacoesPorCanal as $canal => $veiculacoesDoCanal)
                        <div style="margin-bottom: {{ $loop->last ? '0' : '12px' }}; padding-bottom: {{ $loop->last ? '0' : '12px' }}; border-bottom: {{ $loop->last ? 'none' : '1px solid #e5e7eb' }};">
                            {{-- Nome do Canal (apenas 1 vez) --}}
                            <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px; color: {{ $config->cor_primaria }};">
                                {{ $canal }}
                            </div>
                            
                            {{-- Lista de Endereços (sem cidade) --}}
                            @foreach($veiculacoesDoCanal as $veiculacao)
                                @if($veiculacao->inventario && $veiculacao->inventario->endereco)
                                    <div style="font-size: 12px; color: {{ $config->cor_secundaria }}; margin-bottom: 2px; padding-left: 10px;">
                                        • {{ strtoupper($veiculacao->inventario->endereco) }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </td>
            </tr>

            {{-- FREQUÊNCIA TOTAL (SOMA CORRETA DOS SLOTS) --}}
            <tr style="background-color: #f3f4f6;">
                <td style="padding: 8px 15px; font-weight: bold; text-align: right; border: 1px solid #e0e0e0; font-size: 13px;">
                    FREQUÊNCIA TOTAL
                </td>
                <td style="padding: 8px 15px; border: 1px solid #e0e0e0; font-weight: bold; font-size: 13px;">
                    {{ $veiculacoes->sum('slots') }} SLOT{{ $veiculacoes->sum('slots') > 1 ? 'S' : '' }}
                    @if($veiculacoes->count() > 1)
                        <span style="font-size: 12px; color: {{ $config->cor_secundaria }}; font-weight: normal;">
                            ({{ $veiculacoesPorCanal->count() }} {{ $veiculacoesPorCanal->count() > 1 ? 'painéis' : 'painel' }})
                        </span>
                    @endif
                </td>
            </tr>

            {{-- PERÍODO --}}
            <tr>
                <td style="padding: 8px 15px; font-weight: bold; text-align: right; background-color: #f3f4f6; border: 1px solid #e0e0e0; font-size: 13px;">
                    PERÍODO
                </td>
                <td style="padding: 8px 15px; border: 1px solid #e0e0e0; font-weight: bold; font-size: 13px;">
                    {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} A {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        {{-- Imagem da Campanha --}}
        @if($imagemCampanha)
            <div style="padding: 20px 15px; text-align: center; background-color: #f9fafb;">
                <img src="{{ config('app.url') . $imagemCampanha }}" 
                     alt="Campanha" 
                     style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            </div>
        @endif

        {{-- Mensagem Adicional --}}
        @if($config->mensagem_adicional)
            <div style="padding: 15px; background-color: #fffbeb; border-top: 2px solid #fbbf24; border-bottom: 2px solid #fbbf24;">
                <p style="margin: 0; color: #92400e; font-size: 13px; text-align: center;">
                    {{ $config->mensagem_adicional }}
                </p>
            </div>
        @endif

        {{-- Footer --}}
        <div style="padding: 15px; background-color: #f9fafb; border-top: 2px solid #e0e0e0; text-align: center; color: {{ $config->cor_secundaria }}; font-size: 11px;">
            <p style="margin: 3px 0;">{{ $config->texto_rodape_1 }}</p>
            <p style="margin: 3px 0;">{{ $config->texto_rodape_2 }}</p>
            
            @if($config->mostrar_info_contato)
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">
                    <p style="margin: 3px 0; font-weight: bold; color: {{ $config->cor_primaria }}; font-size: 12px;">
                        {{ $config->nome_empresa }}
                    </p>
                    @if($config->telefone || $config->email_contato || $config->site)
                        <p style="margin: 3px 0; font-size: 11px;">
                            @if($config->telefone)
                                📞 {{ $config->telefone }}
                            @endif
                            @if($config->telefone && $config->email_contato)
                                 | 
                            @endif
                            @if($config->email_contato)
                                ✉️ {{ $config->email_contato }}
                            @endif
                        </p>
                        @if($config->site)
                            <p style="margin: 3px 0; font-size: 11px;">
                                🌐 <a href="{{ $config->site }}" style="color: {{ $config->cor_primaria }}; text-decoration: none;">{{ $config->site }}</a>
                            </p>
                        @endif
                    @endif
                    @if($config->endereco)
                        <p style="margin: 3px 0; font-size: 10px;">
                            📍 {{ $config->endereco }}
                        </p>
                    @endif
                </div>
            @endif
            
            @if($veiculacoesPorCanal->count() > 1)
                <p style="margin: 8px 0 3px 0; font-weight: bold; color: {{ $config->cor_primaria }}; font-size: 11px;">
                    Total de {{ $veiculacoesPorCanal->count() }} {{ $veiculacoesPorCanal->count() > 1 ? 'painéis' : 'painel' }} nesta campanha
                </p>
            @endif
        </div>

    </div>
</body>
</html>
