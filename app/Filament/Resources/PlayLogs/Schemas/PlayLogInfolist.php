<?php

namespace App\Filament\Resources\PlayLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PlayLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('inventario.id')
                    ->label('Inventario'),
                TextEntry::make('external_media_id')
                    ->numeric(),
                TextEntry::make('campaign.id')
                    ->label('Campaign'),
                TextEntry::make('played_at')
                    ->dateTime(),
            ]);
    }
}
