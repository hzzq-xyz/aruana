@php
    // Busca a identidade visual do outro módulo para o preview ficar fiel
    $configVisual = \App\Models\ConfiguracaoEmail::first();
    $corPrimaria = $configVisual?->cor_primaria ?? '#dc2626';
    $logo = $configVisual?->logo;
@endphp

<div class="space-y-8 p-4 bg-gray-100 rounded-xl">
    <div class="max-w-xl mx-auto bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <div style="background-color: #10b981; padding: 15px; color: white; text-align: center;">
            <h3 style="margin: 0; font-weight: bold;">PREVIEW: E-MAIL DE APROVAÇÃO</h3>
        </div>
        <div class="p-6">
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" style="max-height: 40px; margin-bottom: 20px;">
            @endif
            
            <h2 style="color: {{ $corPrimaria }}; font-size: 18px; font-weight: bold; margin-bottom: 10px;">
                {{ $get('assunto_aprovacao') ?? 'Seu VT foi aprovado!' }}
            </h2>
            
            <p style="color: #4b5563; white-space: pre-line; margin-bottom: 20px;">
                {{ $get('mensagem_aprovacao') ?? 'O texto configurado aparecerá aqui...' }}
            </p>
            
            <div style="background: #f9fafb; padding: 15px; border-radius: 5px; border-left: 4px solid #10b981; font-size: 14px;">
                <strong>Detalhes:</strong><br>
                <span style="color: #6b7280;">Protocolo: NELA-2026-TESTE</span><br>
                <span style="color: #6b7280;">Painel: Exemplo Nilo Peçanha</span>
            </div>
        </div>
    </div>

    <div class="max-w-xl mx-auto bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <div style="background-color: #ef4444; padding: 15px; color: white; text-align: center;">
            <h3 style="margin: 0; font-weight: bold;">PREVIEW: E-MAIL DE REPROVAÇÃO</h3>
        </div>
        <div class="p-6">
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" style="max-height: 40px; margin-bottom: 20px;">
            @endif
            
            <h2 style="color: #ef4444; font-size: 18px; font-weight: bold; margin-bottom: 10px;">
                {{ $get('assunto_reprovacao') ?? 'Atenção: Seu VT precisa de ajustes' }}
            </h2>
            
            <p style="color: #4b5563; white-space: pre-line; margin-bottom: 20px;">
                {{ $get('mensagem_reprovacao') ?? 'As instruções de correção aparecerão aqui...' }}
            </p>
            
            <div style="text-align: center;">
                <button type="button" style="background-color: {{ $corPrimaria }}; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; border: none;">
                    Reenviar Material
                </button>
            </div>
        </div>
    </div>
</div>