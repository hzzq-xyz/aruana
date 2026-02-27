<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtigoResource\Pages;
use App\Models\Artigo;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// NÚCLEO DA V5
use Filament\Schemas\Schema;

// LAYOUT E COMPONENTES
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;

// ✨ O SEGREDO DO FILAMENT 5: AÇÕES 100% UNIFICADAS ✨
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

// COLUNAS E FILTROS
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

// UTILITÁRIOS
use Illuminate\Support\Str;
use BackedEnum;
use UnitEnum;

class ArtigoResource extends Resource
{
    protected static ?string $model = Artigo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-plus';
    protected static string|UnitEnum|null $navigationGroup = 'Administração';
    protected static ?string $navigationLabel = 'Gestão de Documentos';
    protected static ?string $modelLabel = 'Artigo';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Publicar Novo Documento/Manual')
                    ->description('O PDF submetido será exibido na central de ajuda do cliente conforme a visibilidade definida.')
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título do Artigo')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                        Select::make('categoria')
                            ->label('Categoria')
                            ->options([
                                'Geral' => 'Geral',
                                'Financeiro' => 'Financeiro',
                                'Técnico' => 'Técnico / Prazos',
                                'Checkings' => 'Checkings e Fotos',
                            ])
                            ->required(),

                        Select::make('visibilidade')
                            ->label('Quem pode ver?')
                            ->options([
                                'cliente' => 'Cliente e Admin',
                                'admin' => 'Apenas Admin (Interno)',
                            ])
                            ->default('cliente')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-eye'),

                        FileUpload::make('pdf_path')
                            ->label('Arquivo do Manual (PDF)')
                            ->directory('artigos-pdf')
                            ->required()
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull()
                            ->extraAttributes(['accept' => '.pdf'])
                            ->rules(['mimes:pdf']) 
                            ->validationMessages([
                                'mimes' => 'O arquivo deve ser obrigatoriamente um PDF.',
                            ]),

                        Toggle::make('is_ativo')
                            ->label('Publicado')
                            ->default(true),

                        TextInput::make('ordem')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0),

                        Hidden::make('slug'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('categoria')
                    ->badge()
                    ->color('info'),

                TextColumn::make('visibilidade')
                    ->label('Visibilidade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cliente' => 'success',
                        'admin' => 'warning',
                    }),

                IconColumn::make('pdf_path')
                    ->label('PDF')
                    ->icon(fn ($state) => $state ? 'heroicon-o-document-text' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),

                IconColumn::make('is_ativo')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('categoria')
                    ->options([
                        'Geral' => 'Geral',
                        'Financeiro' => 'Financeiro',
                        'Técnico' => 'Técnico',
                    ]),
                SelectFilter::make('visibilidade')
                    ->options([
                        'cliente' => 'Cliente',
                        'admin' => 'Admin',
                    ]),
            ])
            ->actions([
                // Agora usando a Action unificada da V5
                Action::make('visualizar')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Artigo $record): string => $record->pdf_path ? asset('storage/' . $record->pdf_path) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Artigo $record) => $record->pdf_path),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtigos::route('/'),
            'create' => Pages\CreateArtigo::route('/create'),
            'edit' => Pages\EditArtigo::route('/{record}/edit'),
        ];
    }
}