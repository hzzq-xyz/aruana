<?php

namespace App\Filament\Cliente\Resources\Artigos;

use App\Filament\Cliente\Resources\Artigos\Pages;
use App\Models\Artigo;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

// ✨ O SEGREDO DO FILAMENT 5: Importação Unificada ✨
use Filament\Actions\Action; 

class ArtigoResource extends Resource
{
    protected static ?string $model = Artigo::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Documentação';
    protected static ?string $modelLabel = 'Manual';
    protected static ?string $slug = 'documentacao';
    protected static string | \UnitEnum | null $navigationGroup = 'Suporte';

    /**
     * SEGURANÇA: Filtra para o cliente ver APENAS o que é 'cliente' e está 'ativo'
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('visibilidade', 'cliente')
            ->where('is_ativo', true)
            ->orderBy('ordem', 'asc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título do Manual')
                    ->description(fn (Artigo $record): string => "Categoria: {$record->categoria}")
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('updated_at')
                    ->label('Última atualização')
                    ->dateTime('d/m/Y')
                    ->color('gray'),
            ])
            ->actions([
                // Agora usando a Action Global unificada!
                Action::make('abrir_manual')
                    ->label('Visualizar PDF')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (Artigo $record): string => asset('storage/' . $record->pdf_path))
                    ->openUrlInNewTab(), 
            ])
            ->emptyStateHeading('Nenhum manual disponível no momento.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtigos::route('/'),
        ];
    }

    // Bloqueios de permissão
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
}