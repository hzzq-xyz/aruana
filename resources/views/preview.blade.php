<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Ao Vivo - {{ $media->client_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #0f172a; /* Fundo escuro para destacar o painel */
            background-image: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }

        .painel-container {
            position: relative;
            background: #000;
            /* Moldura grossa para simular o chassi do painel de rua */
            border: 12px solid #1f2937;
            border-radius: 8px;
            /* Sombras simulando o brilho e a estrutura */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8), 
                        inset 0 0 20px rgba(0,0,0,1),
                        0 0 100px rgba(59, 130, 246, 0.1); /* Glow azul leve */
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            
            /* A MÁGICA: Proporção dinâmica baseada no banco de dados */
            aspect-ratio: {{ $media->inventario->largura_px }} / {{ $media->inventario->altura_px }};
            
            /* Limita o tamanho para caber na tela do PC/Celular sem quebrar a proporção */
            max-height: 70vh;
            max-width: 90vw;
            margin: 0 auto;
        }

        .poste {
            width: 60px;
            height: 120px;
            background: linear-gradient(to right, #111827, #374151, #111827);
            margin: 0 auto;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }

        .media-content {
            width: 100%;
            height: 100%;
            /* Object fill preenche 100% da área, mas como o painel tem a mesma proporção, não achata o vídeo! */
            object-fit: fill; 
            z-index: 10;
        }

        /* Efeito de scanline por cima do painel pra dar cara de LED (Opcional) */
        .scanlines {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0) 50%, rgba(0,0,0,0.05) 50%, rgba(0,0,0,0.05));
            background-size: 100% 4px;
            z-index: 20;
            pointer-events: none;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">

    <div class="mb-8 text-center mt-6">
        <h1 class="text-3xl font-bold text-white mb-2 uppercase tracking-wide">Preview de Veiculação</h1>
        <div class="flex items-center justify-center gap-4 text-sm font-medium text-gray-300">
            <span class="bg-gray-800 px-3 py-1 rounded border border-gray-700">Cliente: <span class="text-white">{{ $media->client_name }}</span></span>
            <span class="bg-gray-800 px-3 py-1 rounded border border-gray-700">Painel: <span class="text-blue-400 font-bold">{{ $media->inventario->codigo }}</span></span>
            <span class="bg-gray-800 px-3 py-1 rounded border border-gray-700">Dimensões: <span class="text-green-400">{{ $media->inventario->largura_px }}x{{ $media->inventario->altura_px }}</span></span>
        </div>
    </div>

    <div class="w-full flex flex-col items-center justify-center flex-1">
        
        <div class="painel-container">
            @php
                $filePath = storage_path('app/public/' . $media->file_path);
                $isVideo = false;
                if(file_exists($filePath)) {
                    $mime = mime_content_type($filePath);
                    $isVideo = str_starts_with($mime, 'video/');
                }
            @endphp

            @if($isVideo)
                <video class="media-content" src="{{ asset('storage/' . $media->file_path) }}" autoplay loop muted playsinline></video>
            @else
                <img class="media-content" src="{{ asset('storage/' . $media->file_path) }}" alt="Criativo">
            @endif
            
            <div class="scanlines"></div>
        </div>
        
        <div class="poste"></div>
    </div>

    <div class="mt-8 mb-6 flex gap-4">
        <button onclick="window.close()" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white rounded shadow transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Fechar Preview
        </button>
    </div>

</body>
</html>