<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('nome')
                    ->required(),
                DatePicker::make('data_inicio')
                    ->required(),
                DatePicker::make('data_fim')
                    ->required(),
                Select::make('prioridade')
                    ->options(['filler' => 'Filler', 'normal' => 'Normal', 'exclusiva' => 'Exclusiva'])
                    ->default('normal')
                    ->required(),
                TextInput::make('peso_slot')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options([
            'rascunho' => 'Rascunho',
            'ativa' => 'Ativa',
            'pausada' => 'Pausada',
            'finalizada' => 'Finalizada',
        ])
                    ->default('rascunho')
                    ->required(),
            ]);
    }
}
