<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-sm ring-1 ring-gray-950/5 text-center">
        <x-heroicon-o-server-stack style="width: 64px; height: 64px;" class="mx-auto text-primary-500 mb-4" />
        <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200">Backup em Nuvem - Aruana</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-lg mx-auto">
            Clique no botão <strong>"Fazer Backup para o GitHub"</strong> no canto superior direito para salvar o estado atual do sistema de forma segura.
        </p>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Últimas Sincronizações</h3>
        
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 overflow-hidden">
            @if(count($historico) > 0)
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Data e Hora</th>
                            <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Descrição do Backup</th>
                            <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300 w-32 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($historico as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap">
                                    <x-heroicon-o-calendar class="w-4 h-4 inline-block mr-1 opacity-50"/>
                                    {{ $item['date'] }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    Snapshot automático ({{ $item['message'] }})
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-md text-xs font-medium bg-success-50 text-success-600 dark:bg-success-400/10 dark:text-success-400 ring-1 ring-inset ring-success-600/20">
                                        <x-heroicon-s-check-circle class="w-4 h-4" />
                                        Sucesso
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-gray-500">
                    Nenhum backup registrado pelo sistema ainda.
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>