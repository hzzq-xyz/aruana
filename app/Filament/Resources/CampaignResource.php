<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Schemas\Schema; 
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection; 
use UnitEnum;
use BackedEnum;

// LAYOUTS UNIFICADOS
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

// CAMPOS DE PREENCHIMENTO
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload; // 👈 IMPORTAÇÃO NOVA PARA O UPLOAD DIRETO

// AÇÕES UNIFICADAS
use Filament\Actions\Action;
use Filament\Actions\BulkAction;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    
    protected static ?string $navigationLabel = 'Campanhas DOOH';
    
    protected static ?string $label = 'Campanha';
    
    protected static ?string $pluralLabel = 'Campanhas';
    
    protected static string|UnitEnum|null $navigationGroup = 'Mídia Externa';
    
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])->schema([
                    
                    // COLUNA PRINCIPAL (Esquerda)
                    Grid::make(1)->columnSpan(['default' => 1, 'lg' => 2])->schema([
                        
                        Section::make('Informações Básicas')
                            ->columns(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('Cliente')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(), 
                                    
                                TextInput::make('nome')
                                    ->label('Nome da Campanha')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Section::make('Distribuição de Conteúdo')
                            ->description('Onde e o que vai ser exibido nesta campanha.')
                            ->schema([
                                Select::make('inventarios')
                                    ->relationship('inventarios')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->codigo ?: 'Painel ID: ' . $record->id)
                                    ->label('Painéis / Canais')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                    
                                Select::make('midias')
                                    ->relationship(
                                        name: 'midias',
                                        modifyQueryUsing: function (Builder $query, callable $get) {
                                            $userId = $get('user_id');
                                            
                                            if (! $userId) {
                                                return $query->whereNull('external_media.id');
                                            }
                                            
                                            return $query->where('external_media.user_id', $userId)
                                                         ->whereIn('external_media.status', ['aprovado', 'approved']);
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->file_path ?: 'Mídia ID: ' . $record->id)
                                    ->label('VTs (Mídias Aprovadas)')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    // 👇 A MÁGICA DO UPLOAD DIRETO COMEÇA AQUI 👇
                                    ->createOptionForm([
                                        FileUpload::make('file_path')
                                            ->label('Arquivo do VT (MP4, JPG, etc)')
                                            ->directory('campaign-direct-uploads')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $get) {
                                        $userId = $get('user_id');
                                        
                                        // Bloqueia o upload se o usuário esquecer de selecionar o cliente primeiro
                                        if (! $userId) {
                                            throw new \Exception('Selecione um Cliente primeiro antes de fazer o upload do VT.');
                                        }
                                        
                                        // Salva na tabela "external_media" do cliente e já marca como "aprovado"
                                        $novaMidia = \App\Models\ExternalMedia::create([
                                            'user_id' => $userId,
                                            'file_path' => $data['file_path'],
                                            'status' => 'aprovado',
                                        ]);
                                        
                                        return $novaMidia->getKey();
                                    }),
                            ]),

                        Section::make('Horários de Exibição (Dayparting)')
                            ->description('Define os blocos de horário em que a campanha pode rodar.')
                            ->schema([
                                Repeater::make('schedules')
                                    ->relationship()
                                    ->label('Grade de Horários')
                                    ->columns(3)
                                    ->defaultItems(1)
                                    ->addActionLabel('Adicionar Bloco de Horário')
                                    ->schema([
                                        Select::make('dia_semana')
                                            ->label('Dia da Semana')
                                            ->options([
                                                '0' => 'Domingo',
                                                '1' => 'Segunda-feira',
                                                '2' => 'Terça-feira',
                                                '3' => 'Quarta-feira',
                                                '4' => 'Quinta-feira',
                                                '5' => 'Sexta-feira',
                                                '6' => 'Sábado',
                                            ])
                                            ->required(),
                                            
                                        TimePicker::make('hora_inicio')
                                            ->label('Hora de Início')
                                            ->seconds(false)
                                            ->required(),
                                            
                                        TimePicker::make('hora_fim')
                                            ->label('Hora de Fim')
                                            ->seconds(false)
                                            ->required(),
                                    ]),
                            ]),
                    ]),

                    // COLUNA LATERAL (Direita)
                    Grid::make(1)->columnSpan(['default' => 1, 'lg' => 1])->schema([
                        
                        Section::make('Regras e Período')
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'rascunho' => 'Rascunho',
                                        'ativa' => 'Ativa',
                                        'pausada' => 'Pausada',
                                        'finalizada' => 'Finalizada',
                                    ])
                                    ->default('rascunho')
                                    ->required(),
                                    
                                DatePicker::make('data_inicio')
                                    ->label('Data de Início')
                                    ->required(),
                                    
                                DatePicker::make('data_fim')
                                    ->label('Data de Fim')
                                    ->required(),
                                    
                                Select::make('prioridade')
                                    ->label('Prioridade')
                                    ->options([
                                        'filler' => 'Tapa-buraco',
                                        'normal' => 'Normal',
                                        'exclusiva' => 'Exclusiva',
                                    ])
                                    ->default('normal')
                                    ->required(),
                                    
                                TextInput::make('peso_slot')
                                    ->label('Peso do Slot')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Multiplicador. Ex: Um peso de 2 faz o VT tocar o dobro das vezes.'),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Campanha')
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ativa' => 'success',
                        'pausada' => 'warning',
                        'rascunho' => 'gray',
                        'finalizada' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('data_inicio')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('data_fim')
                    ->label('Fim')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('prioridade')
                    ->label('Prioridade')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'rascunho' => 'Rascunho',
                        'ativa' => 'Ativa',
                        'pausada' => 'Pausada',
                        'finalizada' => 'Finalizada',
                    ]),
                    
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Cliente'),
            ])
            ->actions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn (Campaign $record): string => static::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                BulkAction::make('deletar_selecionados')
                    ->label('Deletar Selecionados')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete())
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}