<?php

namespace App\Filament\Resources\PlayLogs\Pages;

use App\Filament\Resources\PlayLogs\PlayLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPlayLog extends EditRecord
{
    protected static string $resource = PlayLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
