<div class="space-y-4">
    @php
        $properties = $getRecord()->properties ?? [];
        $hasOld = isset($properties['old']);
        $hasAttributes = isset($properties['attributes']);
    @endphp

    @if($hasOld && $hasAttributes)
        {{-- COMPARAÇÃO ANTES/DEPOIS (UPDATED) --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Campo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Antes</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Depois</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $changes = [];
                        foreach ($properties['attributes'] as $key => $newValue) {
                            if (isset($properties['old'][$key]) && $properties['old'][$key] != $newValue) {
                                $changes[$key] = [
                                    'old' => $properties['old'][$key],
                                    'new' => $newValue,
                                ];
                            }
                        }
                    @endphp

                    @if(count($changes) > 0)
                        @foreach($changes as $field => $values)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="rounded bg-red-50 dark:bg-red-900/20 px-3 py-2 text-red-700 dark:text-red-400">
                                        <span class="font-mono text-xs">
                                            @if(is_array($values['old']))
                                                {{ json_encode($values['old']) }}
                                            @elseif(is_bool($values['old']))
                                                {{ $values['old'] ? 'Sim' : 'Não' }}
                                            @elseif(is_null($values['old']))
                                                <em class="text-gray-400">(vazio)</em>
                                            @else
                                                {{ $values['old'] }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="rounded bg-green-50 dark:bg-green-900/20 px-3 py-2 text-green-700 dark:text-green-400">
                                        <span class="font-mono text-xs">
                                            @if(is_array($values['new']))
                                                {{ json_encode($values['new']) }}
                                            @elseif(is_bool($values['new']))
                                                {{ $values['new'] ? 'Sim' : 'Não' }}
                                            @elseif(is_null($values['new']))
                                                <em class="text-gray-400">(vazio)</em>
                                            @else
                                                {{ $values['new'] }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                Nenhuma alteração detectada
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

    @elseif($hasAttributes)
        {{-- APENAS ATTRIBUTES (CREATED) --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Campo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($properties['attributes'] as $key => $value)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="rounded bg-blue-50 dark:bg-blue-900/20 px-3 py-2 text-blue-700 dark:text-blue-400">
                                    <span class="font-mono text-xs">
                                        @if(is_array($value))
                                            {{ json_encode($value) }}
                                        @elseif(is_bool($value))
                                            {{ $value ? 'Sim' : 'Não' }}
                                        @elseif(is_null($value))
                                            <em class="text-gray-400">(vazio)</em>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @elseif($hasOld)
        {{-- APENAS OLD (DELETED) --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Campo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Valor Deletado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($properties['old'] as $key => $value)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="rounded bg-red-50 dark:bg-red-900/20 px-3 py-2 text-red-700 dark:text-red-400">
                                    <span class="font-mono text-xs line-through">
                                        @if(is_array($value))
                                            {{ json_encode($value) }}
                                        @elseif(is_bool($value))
                                            {{ $value ? 'Sim' : 'Não' }}
                                        @elseif(is_null($value))
                                            <em class="text-gray-400">(vazio)</em>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else
        {{-- SEM PROPRIEDADES --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Nenhum detalhe adicional registrado para esta ação.
            </p>
        </div>
    @endif

    {{-- LEGENDA --}}
    <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400 mt-4">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-800"></div>
            <span>Valor Anterior</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800"></div>
            <span>Valor Novo</span>
        </div>
    </div>
</div>