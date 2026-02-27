<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Process;
use UnitEnum;
use BackedEnum;

class SincronizarGithub extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cloud-arrow-up';
    
    protected static string | UnitEnum | null $navigationGroup = 'Sistema';
    
    protected static ?string $title = 'Sincronizar com GitHub';
    
    // A CORREÇÃO FOI AQUI: Removido o 'static' para respeitar a estrutura atual do Filament
    protected string $view = 'filament.pages.vazia'; 

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sincronizar')
                ->label('Fazer Backup para o GitHub')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Sincronizar Sistema Aruana')
                ->modalDescription('Isso vai pegar todo o código atual do servidor e enviar para o seu repositório no GitHub. Deseja continuar?')
                ->action(fn () => $this->executarSincronizacao()),
        ];
    }

    public function executarSincronizacao()
    {
        $user = env('GITHUB_USERNAME');
        $token = env('GITHUB_TOKEN');
        $repo = env('GITHUB_REPO');
        $branch = env('GITHUB_BRANCH', 'main');

        if (!$user || !$token || !$repo) {
            Notification::make()
                ->title('Configuração Incompleta')
                ->body('Faltam as variáveis do GitHub no seu arquivo .env.')
                ->danger()
                ->send();
            return;
        }

        $remoteUrl = "https://{$user}:{$token}@github.com/{$repo}.git";
        $dataHora = now()->format('d/m/Y H:i:s');
        $commitMessage = "Backup Aruana via Sistema - {$dataHora}";
        $path = base_path(); // Pasta raiz do projeto na Hostinger

        try {
            // 1. Inicializa o Git (caso não exista) e força a branch a se chamar 'main'
            Process::path($path)->run("git init");
            Process::path($path)->run("git branch -M {$branch}");

            // 2. Configura a "identidade" do Git (Obrigatório em servidores para o commit não falhar)
            Process::path($path)->run('git config user.email "sistema@aruana.com"');
            Process::path($path)->run('git config user.name "Sistema Aruana"');
            
            // 3. Adiciona todos os arquivos novos ou alterados
            Process::path($path)->run("git add .");
            
            // 4. Executa o commit
            Process::path($path)->run("git commit -m \"{$commitMessage}\"");
            
            // 5. Envia para o GitHub usando --force (Garante que vai sobrescrever qualquer README padrão lá)
            $push = Process::path($path)->run("git push --force {$remoteUrl} {$branch}");

            if ($push->successful()) {
                Notification::make()
                    ->title('Sincronização Concluída!')
                    ->body('O código completo do Aruana foi salvo no GitHub com sucesso.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Erro ao Enviar (Push)')
                    ->body($push->errorOutput() ?: 'O servidor não conseguiu se comunicar com o GitHub.')
                    ->danger()
                    ->persistent()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro Crítico no Servidor')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}