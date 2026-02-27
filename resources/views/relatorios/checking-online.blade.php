<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checking Online | Aruana OOH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Regras para ficar perfeito se o cliente quiser Salvar como PDF */
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-before: always; }
            body { background-color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div class="bg-indigo-900 text-white shadow-lg print:bg-indigo-900 print:text-white">
        <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">ARUANA OOH</h1>
                <p class="text-indigo-200 text-sm">Relatório Fotográfico de Veiculação</p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="bg-white text-indigo-900 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-100 transition shadow">
                    🖨️ Salvar PDF
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 uppercase font-semibold tracking-wider mb-1">Ponto / Endereço</p>
                    <p class="text-xl font-medium text-gray-900">
                        {{ $checkingFoto->inventario->identificacao ?? 'Ponto não identificado' }}
                    </p>
                    <p class="text-gray-600 mt-1">
                        {{ $checkingFoto->inventario->endereco ?? 'Endereço não cadastrado' }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Data do Checking</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $checkingFoto->data_checking ? $checkingFoto->data_checking->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <p class="text-xs text-indigo-500 uppercase font-bold mb-1">Formato</p>
                        <p class="text-lg font-semibold text-indigo-900">
                            {{ $checkingFoto->inventario->formato ?? 'Padrão' }}
                        </p>
                    </div>
                </div>
            </div>
            
            @if($checkingFoto->observacoes)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500 font-semibold mb-1">Observações:</p>
                <p class="text-sm text-gray-700">{{ $checkingFoto->observacoes }}</p>
            </div>
            @endif
        </div>

        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Comprovações Fotográficas</h2>
            
            @if(is_array($checkingFoto->fotos) && count($checkingFoto->fotos) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($checkingFoto->fotos as $foto)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 group print-break">
                            <img src="{{ asset('storage/' . $foto) }}" alt="Foto do Checking" class="w-full h-80 object-cover object-center group-hover:opacity-95 transition">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 text-yellow-800 p-6 rounded-lg text-center border border-yellow-200">
                    Nenhuma foto foi anexada a este checking.
                </div>
            @endif
        </div>

    </div>

    <div class="max-w-5xl mx-auto px-6 py-6 border-t border-gray-200 mt-8 text-center text-sm text-gray-500 print:mt-0">
        Gerado pelo sistema Aruana OOH em {{ date('d/m/Y \à\s H:i') }}
    </div>

</body>
</html>