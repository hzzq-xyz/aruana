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
    protected string $view = 'filament.pages.vazia';

    // FUNÇÃO NOVA: Lê o histórico do Git
    protected function getViewData(): array
    {
        $path = base_path();
        $history = [];

        try {
            // Pega os últimos 10 commits que tenham "via Sistema" no nome
            $log = Process::path($path)->run('git log -n 10 --grep="via Sistema" --pretty=format:"%s|%cd" --date=format:"%d/%m/%Y às %H:%M"');
            
            if ($log->successful() && !empty(trim($log->output()))) {
                $lines = explode("\n", trim($log->output()));
                foreach ($lines as $line) {
                    if (str_contains($line, '|')) {
                        [$message, $date] = explode('|', $line, 2);
                        $history[] = [
                            'message' => str_replace('Backup Aruana via Sistema - ', '', $message), // Limpa o nome
                            'date' => $date
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Falha silenciosa caso o git ainda não tenha commits locais
        }

        return [
            'historico' => $history
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sincronizar')
                ->label('Fazer Backup para o GitHub')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Sincronizar Sistema Aruana')
                ->modalDescription('Isso vai pegar todo o código atual do servidor e enviar para o repositório. Deseja continuar?')
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
            Notification::make()->title('Configuração Incompleta')->danger()->send();
            return;
        }

        $remoteUrl = "https://{$user}:{$token}@github.com/{$repo}.git";
        $dataHora = now()->format('d/m/Y H:i:s');
        $commitMessage = "Backup Aruana via Sistema - {$dataHora}";
        $path = base_path();

        try {
            Process::path($path)->run("git init");
            Process::path($path)->run("git branch -M {$branch}");
            Process::path($path)->run('git config user.email "sistema@aruana.com"');
            Process::path($path)->run('git config user.name "Sistema Aruana"');
            
            Process::path($path)->run("git add .");
            Process::path($path)->run("git commit -m \"{$commitMessage}\"");
            
            $push = Process::path($path)->run("git push --force {$remoteUrl} {$branch}");

            if ($push->successful()) {
                Notification::make()
                    ->title('Sincronização Concluída!')
                    ->body('O código foi salvo no GitHub com sucesso.')
                    ->success()
                    ->send();
                    
                // Recarrega a página para atualizar a tabela na mesma hora
                $this->redirect(request()->header('Referer')); 
            } else {
                Notification::make()
                    ->title('Erro ao Enviar (Push)')
                    ->body('O servidor não conseguiu enviar para o GitHub.')
                    ->danger()->send();
            }
        } catch (\Exception $e) {
            Notification::make()->title('Erro')->body($e->getMessage())->danger()->send();
        }
    }
}