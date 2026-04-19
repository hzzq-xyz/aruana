<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Validacao; // Importações sempre no topo!
use App\Models\CheckingFoto; // <-- NOVO: Importação do Model de Checking
use App\Http\Controllers\PreviewController; 
use App\Http\Controllers\PlayerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Rota existente para o Cliente visualizar a simulação (Link Público)
Route::get('/v/{hash}', function ($hash) {
    // Busca a validação ou dá erro 404 se não achar
    $validacao = Validacao::where('hash', $hash)->firstOrFail();
    $painel = $validacao->inventario;
    
    // Verifica se é imagem para ajustar o player
    $isImagem = in_array(strtolower(pathinfo($validacao->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);

    return view('public.validacao', [
        'validacao' => $validacao,
        'painel'    => $painel,
        'url_midia' => Storage::url($validacao->file_path),
        'is_imagem' => $isImagem,
        'mockup_url'=> $painel->mockup_image ? Storage::url($painel->mockup_image) : null,
    ]);
})->name('validacao.publica');

// --- NOVA ROTA: Preview Interno do Cliente ---
// Usa o middleware 'auth' para que só o cliente logado consiga acessar essa URL
Route::get('/preview/{id}', [PreviewController::class, 'show'])
    ->name('preview.show')
    ->middleware('auth');
    
// Rota Antiga/Existente de Checking
Route::get('/view-checking/{id}', [App\Http\Controllers\CheckingController::class, 'publicShow'])->name('checking.publico');

// --- NOVA ROTA: Relatório Fotográfico Online (Premium) ---
Route::get('/relatorio/checking/{checkingFoto}', function (CheckingFoto $checkingFoto) {
    // Carrega o checking e já traz as informações do inventário (ponto) junto
    $checkingFoto->load('inventario');
    
    return view('relatorios.checking-online', compact('checkingFoto'));
})->name('checking.online');


// O link que o mini-PC vai abrir: nela.opecs.xyz/play/CODIGO-DO-PAINEL
Route::get('/play/{codigo}', [PlayerController::class, 'show']);

// O link interno que o player usa para baixar a lista de vídeos
Route::get('/api/playlist/{codigo}', [PlayerController::class, 'getPlaylist']);

Route::post('/api/log-play', [PlayerController::class, 'logPlay']);