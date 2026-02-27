<div class="space-y-4">
    {{-- Informações Básicas --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h3 class="text-sm font-semibold mb-3">Informações Gerais</h3>
        
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="font-medium text-gray-500">Tipo:</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $record->log_name }}
                    </span>
                </dd>
            </div>
            
            <div>
                <dt class="font-medium text-gray-500">Ação:</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($record->description == 'created') bg-green-100 text-green-800
                        @elseif($record->description == 'updated') bg-yellow-100 text-yellow-800
                        @elseif($record->description == 'deleted') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $record->description }}
                    </span>
                </dd>
            </div>
            
            <div>
                <dt class="font-medium text-gray-500">Modelo:</dt>
                <dd class="mt-1 font-mono text-xs">{{ class_basename($record->subject_type) }}</dd>
            </div>
            
            <div>
                <dt class="font-medium text-gray-500">ID do Registro:</dt>
                <dd class="mt-1 font-mono text-xs">{{ $record->subject_id }}</dd>
            </div>
            
            <div>
                <dt class="font-medium text-gray-500">Usuário:</dt>
                <dd class="mt-1">{{ $record->causer?->name ?? 'Sistema' }}</dd>
            </div>
            
            <div>
                <dt class="font-medium text-gray-500">Data/Hora:</dt>
                <dd class="mt-1">{{ $record->created_at->format('d/m/Y H:i:s') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Propriedades (JSON) --}}
    @if($record->properties && count($record->properties) > 0)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="text-sm font-semibold mb-3">Detalhes das Alterações</h3>
            
            @if(isset($record->properties['attributes']) || isset($record->properties['old']))
                <div class="space-y-3">
                    @if(isset($record->properties['old']) && isset($record->properties['attributes']))
                        {{-- Mostrar comparação (updated) --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campo</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Antes</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Depois</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($record->properties['attributes'] as $key => $value)
                                        @if(isset($record->properties['old'][$key]) && $record->properties['old'][$key] != $value)
                                            <tr>
                                                <td class="px-3 py-2 font-medium">{{ $key }}</td>
                                                <td class="px-3 py-2 text-red-600">
                                                    <code class="text-xs">{{ is_array($record->properties['old'][$key]) ? json_encode($record->properties['old'][$key]) : $record->properties['old'][$key] }}</code>
                                                </td>
                                                <td class="px-3 py-2 text-green-600">
                                                    <code class="text-xs">{{ is_array($value) ? json_encode($value) : $value }}</code>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(isset($record->properties['attributes']))
                        {{-- Mostrar apenas attributes (created) --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campo</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($record->properties['attributes'] as $key => $value)
                                        <tr>
                                            <td class="px-3 py-2 font-medium">{{ $key }}</td>
                                            <td class="px-3 py-2">
                                                <code class="text-xs">{{ is_array($value) ? json_encode($value) : $value }}</code>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @else
                {{-- Mostrar JSON bruto --}}
                <pre class="bg-white dark:bg-gray-900 p-3 rounded border text-xs overflow-x-auto"><code>{{ json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            @endif
        </div>
    @endif
</div>
