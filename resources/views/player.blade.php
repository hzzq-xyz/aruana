<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NELA Player - {{ $codigo }}</title>
    <style>
        body, html { 
            margin: 0; padding: 0; background: #000; 
            overflow: hidden; width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
        }
        #player-container {
            background: #000; position: relative; overflow: hidden; display: none;
        }
        video { 
            width: 100%; height: 100%; 
            object-fit: contain; /* 👈 CORREÇÃO: Não estica mais o vídeo! */
        }
        #loader { position: fixed; color: #fff; font-family: sans-serif; z-index: 100; }
    </style>
</head>
<body>
    <div id="loader">Carregando...</div>
    <div id="player-container">
        <video id="nela-player" autoplay muted playsinline></video>
    </div>

    <script>
        const video = document.getElementById('nela-player');
        const container = document.getElementById('player-container');
        const loader = document.getElementById('loader');
        let playlist = [];
        let currentIndex = 0;
        let painelId = {{ $painel_id }}; // ID vindo do Controller

        async function fetchPlaylist() {
            try {
                const response = await fetch('/api/playlist/{{ $codigo }}');
                const data = await response.json();
                
                if (data.config) {
                    container.style.width = data.config.largura + 'px';
                    container.style.height = data.config.altura + 'px';
                    container.style.display = 'block';
                }

                if (data.playlist && data.playlist.length > 0) {
                    playlist = data.playlist;
                    loader.style.display = 'none';
                    if (video.paused || !video.src) playNext();
                } else {
                    loader.innerText = 'Sem campanhas ativas.';
                    loader.style.display = 'block';
                }
            } catch (e) {
                console.error("Erro na API:", e);
            }
        }

        function playNext() {
            if (playlist.length === 0) return;

            const media = playlist[currentIndex];
            video.src = media.url;
            video.play().catch(e => setTimeout(playNext, 1000));

            // ENVIAR RELATÓRIO DE EXIBIÇÃO (Proof of Play)
            fetch('/api/log-play', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({
                    inventario_id: painelId,
                    media_id: media.id,
                    campaign_id: media.campaign_id
                })
            });

            currentIndex = (currentIndex + 1) % playlist.length;
        }

        video.onended = playNext;
        setInterval(fetchPlaylist, 120000);
        fetchPlaylist();
    </script>
</body>
</html>