<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtendimentoResource\Pages;
use App\Models\Atendimento;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use UnitEnum;
use BackedEnum;

class AtendimentoResource extends Resource
{
    protected static ?string $model = Atendimento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $label = 'Atendimento';
    
    protected static ?string $pluralLabel = 'Atendimentos';
    
    protected static string|UnitEnum|null $navigationGroup = 'Configurações';
    
    protected static ?int $navigationSort = 98;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('nome')
                ->label('Nome do Atendimento/Comercial')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->placeholder('Ex: ANA KARINA'),
            
            Toggle::make('ativo')
                ->label('Ativo')
                ->default(true)
                ->helperText('Desative para ocultar nas opções de envio'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                IconColumn::make('ativo')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ativo')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('nome');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAtendimentos::route('/'),
            'create' => Pages\CreateAtendimento::route('/create'),
            'edit' => Pages\EditAtendimento::route('/{record}/edit'),
        ];
    }
}
