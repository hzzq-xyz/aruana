<?php

namespace App\Filament\Resources\PlayLogs\Pages;

use App\Filament\Resources\PlayLogs\PlayLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayLog extends ViewRecord
{
    protected static string $resource = PlayLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
