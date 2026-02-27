<?php

namespace App\Filament\Resources\ConfiguracaoAlertaResource\Pages;

use App\Filament\Resources\ConfiguracaoAlertaResource;
use App\Models\ConfiguracaoAlerta;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfiguracaoAlerta extends EditRecord
{
    protected static string $resource = ConfiguracaoAlertaResource::class;

    /**
     * Como esta é uma página de configurações (Singleton), 
     * precisamos dizer qual record carregar, já que não temos ID na URL.
     */
    public function mount($record = null): void
    {
        // Busca a configuração existente ou cria a primeira se estiver vazio
        $record = ConfiguracaoAlerta::getConfig()->id;

        parent::mount($record);
    }

    /**
     * Remove o botão de "Excluir" para você não apagar 
     * as configurações do sistema por acidente.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Redireciona para a mesma página após salvar
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}