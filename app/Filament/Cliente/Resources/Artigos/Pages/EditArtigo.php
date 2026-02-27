<?php

namespace App\Filament\Cliente\Resources\Artigos\Pages;

use App\Filament\Cliente\Resources\Artigos\ArtigoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtigo extends EditRecord
{
    protected static string $resource = ArtigoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
