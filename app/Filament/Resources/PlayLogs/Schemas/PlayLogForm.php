<?php

namespace App\Filament\Resources\PlayLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlayLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventario_id')
                    ->relationship('inventario', 'id')
                    ->required(),
                TextInput::make('external_media_id')
                    ->required()
                    ->numeric(),
                Select::make('campaign_id')
                    ->relationship('campaign', 'id')
                    ->required(),
                DateTimePicker::make('played_at')
                    ->required(),
            ]);
    }
}
