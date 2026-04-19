<?php

namespace App\Filament\Resources\PlayLogResource\Pages;

use App\Filament\Resources\PlayLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPlayLogs extends ListRecords
{
    protected static string $resource = PlayLogResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PlayLogResource\Widgets\PlayLogStats::class,
        ];
    }

    // Configuração opcional: faz os widgets ocuparem toda a largura
    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}