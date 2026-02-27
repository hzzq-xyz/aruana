<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario; 
use App\Models\ExternalMedia;
use Illuminate\Support\Facades\Log;

class ValidadorController extends Controller
{
    /**
     * 1. Lista os painéis para a ferramenta externa preencher o Select
     */
    public function listarConfiguracoes()
    {
        // Busca apenas painéis do tipo 'On' (Digitais)
        $paineis = Inventario::where('tipo', 'On')
            ->select(
                'id', 
                // O nome concatenado garante que o validador encontre o histórico corretamente
                \DB::raw("CONCAT(canal, ' - ', endereco) as name"),
                'largura_px as w', 
                'altura_px as h', 
                'tempo_maximo as d',
                'mockup_image as mockup_img',
                'mockup_css'
            )->get();

        // Converte o caminho relativo da imagem para uma URL completa
        $paineis->transform(function ($painel) {
            if ($painel->mockup_img) {
                $painel->mockup_img = asset('storage/' . $painel->mockup_img);
            }
            // Se o tempo_maximo estiver vazio ou zero no banco, assumimos 10 segundos
            if (!$painel->d) {
                $painel->d = 10;
            }
            return $painel;
        });

        return response()->json($paineis);
    }

    /**
     * 2. Recebe o ficheiro validado e guarda no Filament (ExternalMedia)
     */
    public function receberWebhook(Request $request)
    {
        try {
            $media = ExternalMedia::create([
                'protocol_id'   => (string) $request->input('slot_id'), 
                'file_url'      => $request->input('file_url'),
                'original_name' => $request->input('original_name'),
                
                // --- NOVOS CAMPOS DO FORMULÁRIO ---
                'client_name'   => $request->input('client_name'), 
                'period'        => $request->input('period'),      
                'pi_number'     => $request->input('pi_number'),   
                // ---------------------------------

                'panel_config'  => $request->input('panel_config'),
                'approved_at'   => $request->input('approved_at'),
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Mídia registrada com sucesso',
                'id' => $media->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erro no Webhook de Validação: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => 'Falha interna ao registrar mídia.'
            ], 500);
        }
    }
}