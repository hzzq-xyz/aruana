<?php

namespace App\Filament\Resources\AtendimentoResource\Pages;

use App\Filament\Resources\AtendimentoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListAtendimentos extends ListRecords
{
    protected static string $resource = AtendimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
