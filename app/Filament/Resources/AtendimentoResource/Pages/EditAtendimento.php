<?php

namespace App\Filament\Resources\AtendimentoResource\Pages;

use App\Filament\Resources\AtendimentoResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditAtendimento extends EditRecord
{
    protected static string $resource = AtendimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
