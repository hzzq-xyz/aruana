<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckingResource\Pages;
use App\Models\CheckingFoto;
use App\Models\Inventario;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

// IMPORTAÇÕES UNIFICADAS DO FILAMENT 5
use Filament\Actions\Action; 
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;
use BackedEnum;

class CheckingResource extends Resource
{
    protected static ?string $model = CheckingFoto::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-camera';
    
    protected static ?string $navigationLabel = 'Checking Online';
    
    protected static ?string $label = 'Checking';
    
    protected static ?string $pluralLabel = 'Checkings Online';

    protected static string|UnitEnum|null $navigationGroup = 'Mídia Externa';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nova Amostragem / Checking Individual')
                ->description('Para envios em massa de vários painéis, use o botão "Importação em Lote" na listagem.')
                ->schema([
                    FileUpload::make('fotos')
                        ->label('Fotos do Painel')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->directory('checkings')
                        ->live()
                        ->required()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (!$state) return;
                            
                            $file = is_array($state) ? reset($state) : $state;
                            if (! $file instanceof TemporaryUploadedFile) return;

                            $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                            
                            // Regex para limpar (1), -1, _10 etc.
                            $codigoLimpo = trim(preg_replace('/[\s\-_]*\(.*\)|[\s\-_]+\d+$/', '', $rawName));

                            $painel = Inventario::where('codigo', $codigoLimpo)->first();

                            if ($painel) {
                                $set('inventario_id', $painel->id);
                                Notification::make()
                                    ->title('Painel Identificado!')
                                    ->body("Código: {$codigoLimpo}")
                                    ->success()
                                    ->send();
                            }
                        }),

                    Select::make('inventario_id')
                        ->label('Painel (Individual)')
                        ->relationship('inventario', 'codigo')
                        ->required(fn (Get $get) => empty($get('relatorio_detalhado'))) // Só obriga se não for relatório em lote
                        ->searchable()
                        ->live()
                        ->helperText('Preenchido automaticamente no upload individual.'),

                    DatePicker::make('data_checking')
                        ->label('Data do Checking')
                        ->default(now())
                        ->required(),

                    Textarea::make('observacoes')
                        ->label('Notas / Observações')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // COLUNA DE RESUMO: Mostra todos os códigos se for em lote, ou o código individual
                Tables\Columns\TextColumn::make('resumo')
                    ->label('Painéis no Relatório')
                    ->state(function (CheckingFoto $record) {
                        if ($record->relatorio_detalhado) {
                            return collect($record->relatorio_detalhado)
                                ->map(fn($item) => $item['info']['codigo'])
                                ->implode(', ');
                        }
                        return $record->inventario?->codigo ?? 'Individual';
                    })
                    ->badge()
                    ->color(fn ($state) => str_contains($state, ',') ? 'success' : 'gray')
                    ->searchable(query: function ($query, $search) {
                        return $query->where('relatorio_detalhado', 'like', "%{$search}%")
                                     ->orWhereHas('inventario', fn($q) => $q->where('codigo', 'like', "%{$search}%"));
                    }),
                
                Tables\Columns\TextColumn::make('data_checking')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('fotos')
                    ->label('Amostras')
                    ->circular()
                    ->stacked()
                    ->limit(3),
            ])
            ->actions([
                // BOTÃO DE LINK PÚBLICO
                Action::make('view_public')
                    ->label('Link Público')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->url(fn ($record) => route('checking.publico', $record->id))
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckingFotos::route('/'),
            'create' => Pages\CreateCheckingFoto::route('/create'),
            'edit' => Pages\EditCheckingFoto::route('/{record}/edit'),
        ];
    }
}