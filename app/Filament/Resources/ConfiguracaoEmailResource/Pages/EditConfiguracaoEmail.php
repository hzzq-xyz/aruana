<?php

namespace App\Filament\Resources\ConfiguracaoEmailResource\Pages;

use App\Filament\Resources\ConfiguracaoEmailResource;
use App\Models\ConfiguracaoEmail;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditConfiguracaoEmail extends EditRecord
{
    protected static string $resource = ConfiguracaoEmailResource::class;
    
    protected static ?string $title = 'Configurações do Template de Email';

    /**
     * Sempre carrega o primeiro (e único) registro
     */
    public function mount(int | string $record = null): void
    {
        $config = ConfiguracaoEmail::getConfig();
        
        $this->record = $config;
        
        $this->fillForm();
        
        $this->previousUrl = static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetar')
                ->label('Resetar Padrões')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'cor_banner' => '#dc2626',
                        'cor_primaria' => '#dc2626',
                        'cor_secundaria' => '#666666',
                        'nome_empresa' => 'NELA COMUNICAÇÃO',
                        'texto_rodape_1' => 'Este é um informativo automático gerado pelo sistema NELA',
                        'texto_rodape_2' => 'Para mais informações, entre em contato conosco',
                        'mostrar_logo' => true,
                        'mostrar_info_contato' => true,
                    ]);
                    
                    $this->fillForm();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Configurações resetadas!')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
