<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('nome'),
                TextEntry::make('data_inicio')
                    ->date(),
                TextEntry::make('data_fim')
                    ->date(),
                TextEntry::make('prioridade')
                    ->badge(),
                TextEntry::make('peso_slot')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
