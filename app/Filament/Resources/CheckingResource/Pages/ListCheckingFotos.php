<?php

namespace App\Filament\Resources\CheckingResource\Pages;

use App\Filament\Resources\CheckingResource;
use App\Models\CheckingFoto;
use App\Models\Inventario;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

class ListCheckingFotos extends ListRecords
{
    protected static string $resource = CheckingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // AÇÃO DE IMPORTAÇÃO EM LOTE CONSOLIDADA
            Actions\Action::make('importarLote')
                ->label('Importação em Lote')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Gerar Relatório Único (Amostragem)')
                ->modalDescription('Todas as fotos subidas aqui gerarão UM ÚNICO link para o cliente, organizadas por painel.')
                ->form([
                    FileUpload::make('arquivos')
                        ->label('Arraste todas as fotos da campanha aqui')
                        ->multiple()
                        ->image()
                        ->directory('checkings')
                        ->preserveFilenames() // Mantém o nome original para o Regex funcionar
                        ->required(),
                ])
                ->action(function (array $data) {
                    $arquivos = $data['arquivos'];
                    $relatorio = [];
                    $todasAsFotos = [];

                    foreach ($arquivos as $caminho) {
                        $nomeArquivo = basename($caminho);
                        
                        // Regex para limpar o código (ex: NILO-01 (1) vira NILO-01)
                        $codigo = trim(preg_replace('/[\s\-_]*\(.*\)|[\s\-_]+\d+$/', '', pathinfo($nomeArquivo, PATHINFO_FILENAME)));
                        
                        $painel = Inventario::where('codigo', $codigo)->first();

                        if ($painel) {
                            // Organiza as informações e agrupa as fotos por ID de painel
                            if (!isset($relatorio[$painel->id])) {
                                $relatorio[$painel->id]['info'] = [
                                    'canal' => $painel->canal,
                                    'local' => "{$painel->cidade} - {$painel->endereco}",
                                    'codigo' => $painel->codigo
                                ];
                            }
                            
                            $relatorio[$painel->id]['fotos'][] = $caminho;
                            $todasAsFotos[] = $caminho;
                        }
                    }

                    if (count($relatorio) > 0) {
                        // CRIA UM ÚNICO REGISTRO MESTRE COM TUDO
                        CheckingFoto::create([
                            'inventario_id' => null, // Nulo pois contém múltiplos painéis
                            'fotos' => $todasAsFotos,
                            'relatorio_detalhado' => $relatorio,
                            'data_checking' => now(),
                        ]);

                        Notification::make()
                            ->title('Relatório consolidado criado!')
                            ->body('Todos os painéis identificados foram agrupados em um único link.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Nenhum painel identificado')
                            ->body('Verifique se os nomes dos arquivos correspondem aos códigos do Inventário.')
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()->label('Novo Individual'),
        ];
    }
}