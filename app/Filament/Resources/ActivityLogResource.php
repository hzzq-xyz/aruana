<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use UnitEnum;
use BackedEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Logs de Atividades';
    
    protected static ?string $label = 'Log de Atividade';
    
    protected static ?string $pluralLabel = 'Logs de Atividades';
    
    protected static string|UnitEnum|null $navigationGroup = 'Sistema';
    
    protected static ?int $navigationSort = 100;

    public static function table(Table $table): Table
    {
        return $table
        
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('log_name')
                
                    ->label('Tipo')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color('primary'),
                
                TextColumn::make('description')
                    ->label('Ação')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('subject_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : 'N/A')
                    ->sortable(),
                
                TextColumn::make('subject_id')
                    ->label('ID Registro')
                    ->sortable(),
                
                TextColumn::make('causer.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->default('Sistema')
                    ->weight('medium'),
                
                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Tipo')
                    ->options([
                        'default' => 'Padrão',
                        'renovacao' => 'Renovação',
                        'pauta' => 'Pauta',
                        'veiculacao' => 'Veiculação',
                    ]),
                
                Tables\Filters\SelectFilter::make('description')
                    ->label('Ação')
                    ->options([
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Deletado',
                    ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}