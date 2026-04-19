<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayLogResource\Pages;
use App\Models\PlayLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use BackedEnum;

class PlayLogResource extends Resource
{
    protected static ?string $model = PlayLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Relatório de Exibições';
    protected static ?string $pluralLabel = 'Logs de Exibição';
    protected static string|UnitEnum|null $navigationGroup = 'Checking / Auditoria';
    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('played_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('campaign.nome')
                    ->label('Campanha')
                    ->searchable()
                    ->sortable()
                    ->summarize(
                        Tables\Columns\Summarizers\Count::make()
                            ->label('Total de Exibições')
                    ),

                TextColumn::make('inventario.codigo')
                    ->label('Painel')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('media.file_path')
                    ->label('VT / Mídia')
                    ->description(fn (PlayLog $record): string => "ID Mídia: {$record->external_media_id}")
                    ->limit(40),
            ])
            ->defaultSort('played_at', 'desc')
            ->filters([
                // 👇 FILTRO DE CAMPANHA BLINDADO 👇
                SelectFilter::make('campaign_id')
                    ->label('Campanha')
                    ->relationship('campaign', 'nome')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nome ?: "Campanha ID: {$record->id}")
                    ->searchable()
                    ->preload(),

                // 👇 FILTRO DE PAINEL BLINDADO 👇
                SelectFilter::make('inventario_id')
                    ->label('Painel')
                    ->relationship('inventario', 'codigo')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->codigo ?: "Painel ID: {$record->id}")
                    ->searchable()
                    ->preload(),

                Filter::make('played_at')
                    ->form([
                        DatePicker::make('desde')->label('Data Início'),
                        DatePicker::make('ate')->label('Data Fim'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn ($q) => $q->whereDate('played_at', '>=', $data['desde']))
                            ->when($data['ate'], fn ($q) => $q->whereDate('played_at', '<=', $data['ate']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) $indicators[] = 'Início: ' . \Carbon\Carbon::parse($data['desde'])->format('d/m/Y');
                        if ($data['ate'] ?? null) $indicators[] = 'Fim: ' . \Carbon\Carbon::parse($data['ate'])->format('d/m/Y');
                        return $indicators;
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayLogs::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
}