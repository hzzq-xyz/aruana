<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function getPlaylist($codigo)
    {
        // 1. Busca o painel pelas configurações
        $painel = Inventario::where('codigo', $codigo)->firstOrFail();
        
        $hoje = now()->format('Y-m-d');
        $agora = now()->format('H:i:s');
        $diaSemana = now()->dayOfWeek; // 0 (Dom) a 6 (Sáb)

        // 2. Busca campanhas que devem passar AGORA
        $campanhas = Campaign::where('status', 'ativa')
            ->whereDate('data_inicio', '<=', $hoje)
            ->whereDate('data_fim', '>=', $hoje)
            ->whereHas('inventarios', fn($q) => $q->where('inventario_id', $painel->id))
            ->whereHas('schedules', fn($q) => 
                $q->where('dia_semana', $diaSemana)
                  ->where('hora_inicio', '<=', $agora)
                  ->where('hora_fim', '>=', $agora)
            )
            ->with('midias')
            ->get();

        // 3. Monta a fila de reprodução (Playlist)
        $playlist = [];
        foreach ($campanhas as $campanha) {
            foreach ($campanha->midias as $midia) {
                $playlist[] = [
                    'id' => $midia->id,
                    'campaign_id' => $campanha->id, // 👈 ESSENCIAL PARA O RELATÓRIO
                    'url' => asset('storage/' . $midia->file_path),
                    'duracao' => ($painel->tempo ?? 10) * 1000, 
                ];
            }
        }

        // 4. Retorna para o Player
        return response()->json([
            'config' => [
                'id' => $painel->id, // 👈 PASSANDO O ID PARA O PLAYER
                'largura' => $painel->largura ?? 1920,
                'altura' => $painel->altura ?? 1080,
                'slots' => $painel->total_slots ?? 10,
            ],
            'playlist' => $playlist
        ]);
    }

    public function show($codigo)
    {
        // Precisamos buscar o painel aqui também para pegar o ID real dele
        $painel = Inventario::where('codigo', $codigo)->firstOrFail();

        return view('player', [
            'codigo' => $codigo,
            'painel_id' => $painel->id // 👈 ENVIA O ID PARA O JAVASCRIPT DO PLAYER
        ]);
    }

    public function logPlay(Request $request)
    {
        // Validação simples para evitar lixo no banco
        if (!$request->inventario_id || !$request->media_id) {
            return response()->json(['error' => 'Dados incompletos'], 400);
        }

        DB::table('play_logs')->insert([
            'inventario_id' => $request->inventario_id,
            'external_media_id' => $request->media_id,
            'campaign_id' => $request->campaign_id,
            'played_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}