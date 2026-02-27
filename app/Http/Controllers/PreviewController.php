<?php

namespace App\Http\Controllers;

use App\Models\ExternalMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function show($id)
    {
        // 1. Busca a mídia que o cliente acabou de subir (ExternalMedia ao invés de Validacao)
        $media = ExternalMedia::with('inventario')->findOrFail($id);
        $painel = $media->inventario;
        
        // 2. Verifica se é imagem para ajustar o player da sua view
        $isImagem = in_array(strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);

        // 3. Retorna a SUA view existente (public.validacao), passando os dados que ela já espera!
        return view('public.validacao', [
            'validacao' => $media, // Passamos a nossa $media fingindo ser a $validacao pra view não quebrar
            'painel'    => $painel,
            'url_midia' => Storage::url($media->file_path),
            'is_imagem' => $isImagem,
            'mockup_url'=> $painel->mockup_image ? Storage::url($painel->mockup_image) : null,
        ]);
    }
}