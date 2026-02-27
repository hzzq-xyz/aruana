@php
    $config = \App\Models\ConfiguracaoEmail::getConfig();
@endphp

<div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; border: 2px solid #e0e0e0; font-family: Arial, sans-serif; font-size: 13px;">
        
        {{-- Header --}}
        <div style="padding: 15px; border-bottom: 2px solid #e0e0e0; display: flex; align-items: center; gap: 10px;">
            @if($config->mostrar_logo && $config->logo)
                <img src="{{ Storage::url($config->logo) }}" 
                     alt="{{ $config->nome_empresa }}" 
                     style="max-height: 40px; width: auto;">
            @else
                <div style="background-color: {{ $config->cor_primaria }}; width: 40px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-weight: bold; font-size: 20px;">i</span>
                </div>
            @endif
            
            <div>
                <div style="color: {{ $config->cor_primaria }}; font-size: 14px; font-weight: bold;">Informativo</div>
                <div style="color: {{ $config->cor_primaria }}; font-size: 14px; font-weight: bold;">de veiculação</div>
            </div>
        </div>

        {{-- Banner --}}
        <div style="background-color: {{ $config->cor_banner }}; color: white; text-align: center; padding: 10px; font-size: 14px; font-weight: bold; letter-spacing: 1px;">
            INFORMATIVO DIGITAL
        </div>

        {{-- Info --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tr style="background-color: #f3f4f6;">
                <td style="padding: 8px 12px; font-weight: bold; text-align: right; width: 35%; border: 1px solid #e0e0e0;">
                    CLIENTE
                </td>
                <td style="padding: 8px 12px; border: 1px solid #e0e0e0; font-weight: bold;">
                    EXEMPLO S.A.
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; text-align: right; background-color: #f3f4f6; border: 1px solid #e0e0e0;">
                    CAMPANHA
                </td>
                <td style="padding: 8px 12px; border: 1px solid #e0e0e0; font-weight: bold;">
                    CAMPANHA EXEMPLO
                </td>
            </tr>
            <tr style="background-color: #f3f4f6;">
                <td style="padding: 8px 12px; font-weight: bold; text-align: right; border: 1px solid #e0e0e0;">
                    PERÍODO
                </td>
                <td style="padding: 8px 12px; border: 1px solid #e0e0e0; font-weight: bold;">
                    01/01/2026 A 31/01/2026
                </td>
            </tr>
        </table>

        {{-- Mensagem Adicional --}}
        @if($config->mensagem_adicional)
            <div style="padding: 15px; background-color: #fffbeb; border-top: 2px solid #fbbf24; border-bottom: 2px solid #fbbf24;">
                <p style="margin: 0; color: #92400e; font-size: 11px; text-align: center;">
                    {{ $config->mensagem_adicional }}
                </p>
            </div>
        @endif

        {{-- Footer --}}
        <div style="padding: 15px; background-color: #f9fafb; border-top: 2px solid #e0e0e0; text-align: center; color: {{ $config->cor_secundaria }}; font-size: 10px;">
            <p style="margin: 3px 0;">{{ $config->texto_rodape_1 }}</p>
            <p style="margin: 3px 0;">{{ $config->texto_rodape_2 }}</p>
            
            @if($config->mostrar_info_contato)
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0;">
                    <p style="margin: 3px 0; font-weight: bold; color: {{ $config->cor_primaria }}; font-size: 11px;">
                        {{ $config->nome_empresa }}
                    </p>
                    @if($config->telefone || $config->email_contato)
                        <p style="margin: 3px 0; font-size: 10px;">
                            @if($config->telefone)
                                📞 {{ $config->telefone }}
                            @endif
                            @if($config->telefone && $config->email_contato) | @endif
                            @if($config->email_contato)
                                ✉️ {{ $config->email_contato }}
                            @endif
                        </p>
                    @endif
                    @if($config->site)
                        <p style="margin: 3px 0; font-size: 10px;">
                            🌐 {{ $config->site }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

    </div>
    
    <div style="text-align: center; margin-top: 15px; color: #666; font-size: 12px;">
        ℹ️ Esta é uma prévia simplificada. O email real terá todas as plataformas listadas.
    </div>
</div>
