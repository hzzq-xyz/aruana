<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checking Online - Relatório de Amostragem</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900">
    <div class="max-w-5xl mx-auto py-10 px-4">
        
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border-t-4" style="border-color: {{ $config->cor_primaria ?? '#2563eb' }}">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                @if($config?->logo)
                    <img src="{{ asset('storage/' . $config->logo) }}" class="h-12">
                @else
                    <h1 class="text-2xl font-bold italic">NELA</h1>
                @endif
                <div class="text-center md:text-right">
                    <h2 class="text-xl font-bold text-gray-800">Relatório de Veiculação</h2>
                    <p class="text-gray-500 text-sm">Data do Checking: {{ $record->data_checking->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        @if($record->relatorio_detalhado)
            @foreach($record->relatorio_detalhado as $painelId => $item)
                <div class="mb-12">
                    <div class="bg-gray-800 text-white p-4 rounded-t-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                        <div>
                            <span class="font-bold text-lg block">{{ $item['info']['canal'] }}</span>
                            <span class="text-sm opacity-80">{{ $item['info']['local'] }}</span>
                        </div>
                        <span class="bg-white/20 px-3 py-1 rounded text-xs font-mono uppercase tracking-wider">
                            ID: {{ $item['info']['codigo'] }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-b-xl shadow-sm border border-gray-200 border-t-0">
                        @foreach($item['fotos'] as $foto)
                            <div class="group relative border rounded-lg overflow-hidden bg-gray-50 aspect-video">
                                <img src="{{ asset('storage/' . $foto) }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     alt="Checking {{ $item['info']['codigo'] }}">
                                
                                <div class="absolute bottom-0 left-0 right-0 p-2 bg-black/40 text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">
                                    Visualização: {{ $item['info']['codigo'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white p-8 rounded-xl text-center border shadow-sm">
                <p class="text-gray-500 italic text-sm">Nenhuma informação detalhada encontrada para este relatório.</p>
            </div>
        @endif

        <footer class="mt-12 text-center text-gray-400 text-sm border-t pt-6">
            <p>&copy; {{ date('Y') }} NELA Comunicação - Sistema de Gestão de Mídia Externa (OOH)</p>
            <p class="text-[10px] uppercase mt-1 tracking-widest">Tecnologia para Painéis Digitais</p>
        </footer>
    </div>
</body>
</html>