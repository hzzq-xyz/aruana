<x-filament-panels::page>
    <x-filament::section>
        <div style="text-align: center; padding: 20px;">
            <x-heroicon-o-server-stack style="width: 64px; height: 64px; margin: 0 auto; color: #6b7280;" />
            <h2 style="font-size: 1.5rem; font-weight: bold; margin-top: 15px; color: #374151;">Backup em Nuvem - Aruana</h2>
            <p style="color: #6b7280; margin-top: 10px; line-height: 1.5;">
                Clique no botão <strong>"Fazer Backup para o GitHub"</strong> no canto superior direito para salvar o estado atual do sistema de forma segura.
            </p>
        </div>
    </x-filament::section>

    <x-filament::section heading="Últimas Sincronizações">
        @if(count($historico) > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151;">Data e Hora</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151;">Descrição do Backup</th>
                            <th style="padding: 12px 16px; font-weight: 600; text-align: center; color: #374151;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historico as $item)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 12px 16px; color: #4b5563; white-space: nowrap;">
                                    <x-heroicon-o-calendar style="width: 16px; height: 16px; display: inline-block; vertical-align: text-bottom; margin-right: 6px; color: #9ca3af;" />
                                    {{ $item['date'] }}
                                </td>
                                <td style="padding: 12px 16px; color: #4b5563;">
                                    Snapshot automático ({{ $item['message'] }})
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #d1fae5; color: #065f46;">
                                        <x-heroicon-s-check-circle style="width: 16px; height: 16px;" />
                                        Sucesso
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                Nenhum backup registrado pelo sistema ainda.
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>