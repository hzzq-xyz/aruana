<?php

namespace App\Filament\Cliente\Resources\EnvioMidias\Pages;

use App\Filament\Cliente\Resources\EnvioMidias\EnvioMidiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnvioMidias extends ListRecords
{
    protected static string $resource = EnvioMidiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Botão que abre o formulário de envio (Wizard)
            Actions\CreateAction::make()
                ->label('Novo Envio e Validação'),
        ];
    }
}