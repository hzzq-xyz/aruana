<?php

namespace App\Filament\Cliente\Resources\EnvioMidias;

use App\Filament\Cliente\Resources\EnvioMidias\Pages;
use App\Models\ExternalMedia;
use App\Models\Inventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; 
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

// AJUSTE DE IMPORTAÇÃO PARA FILAMENT 5 (SCHEMAS)
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

// IMPORTAÇÕES UNIFICADAS DO FILAMENT 5
use Filament\Actions\Action; 
use Filament\Actions\DeleteAction; 

use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Validation\ValidationException;

class EnvioMidiaResource extends Resource
{
    protected static ?string $model = ExternalMedia::class;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cloud-arrow-up';
    
    protected static ?string $navigationLabel = 'Enviar Mídia';
    
    protected static ?string $modelLabel = 'Mídia';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            
            Section::make('Validação do Criativo')
                ->description('Selecione o canal, a localização e faça upload do arquivo')
                ->schema([
                    
                    // 1. SELEÇÃO DO CANAL
                    Select::make('canal_selecionado')
                        ->label('Canal / Produto')
                        ->placeholder('Ex: Mega Painel Digital')
                        ->options(fn () => Inventario::distinct()->pluck('canal', 'canal'))
                        ->live()
                        ->required()
                        ->dehydrated(false)
                        ->afterStateUpdated(fn (Set $set) => $set('inventario_id', null)),

                    // 2. SELEÇÃO DO ENDEREÇO
                    Select::make('inventario_id')
                        ->label('Localização / Endereço')
                        ->placeholder('Selecione a cidade e endereço...')
                        ->options(function (Get $get) {
                            $canal = $get('canal_selecionado');
                            
                            if (!$canal) {
                                return [];
                            }

                            return Inventario::where('canal', $canal)
                                ->get()
                                ->mapWithKeys(fn ($item) => [
                                    $item->id => "{$item->cidade} - {$item->endereco}"
                                ]);
                        })
                        ->required()
                        ->live()
                        ->searchable()
                        ->hidden(fn (Get $get) => !$get('canal_selecionado'))
                        ->helperText('Selecione o ponto exato de exibição')
                        ->afterStateUpdated(function (Set $set) {
                            $set('file_path', null);
                            $set('painel_info', '');
                        }),

                    FileUpload::make('file_path')
                        ->label('Arquivo de Mídia')
                        ->directory('vts-validacao')
                        ->acceptedFileTypes(['video/*', 'image/*'])
                        ->maxSize(102400) // 100MB
                        ->required()
                        ->helperText('Máximo 100MB - Vídeos ou Imagens')
                        ->live()
                        ->rules([
                            fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                $painelId = $get('inventario_id');
                                if (!$painelId || !$value) return;

                                $painel = Inventario::find($painelId);
                                if (!$painel) return;

                                if ($value instanceof TemporaryUploadedFile) {
                                    $filePath = $value->getRealPath();
                                    $mimeType = $value->getMimeType();
                                } elseif (is_string($value)) {
                                    $filePath = storage_path('app/public/' . $value);
                                    if (!file_exists($filePath)) return;
                                    $mimeType = mime_content_type($filePath);
                                } else {
                                    return;
                                }

                                if (str_starts_with($mimeType, 'image/')) {
                                    $size = @getimagesize($filePath);
                                    if ($size) {
                                        if ($painel->largura_px && $size[0] !== (int) $painel->largura_px) {
                                            $fail("A largura da imagem ({$size[0]}px) não bate com o exigido ({$painel->largura_px}px).");
                                        }
                                        if ($painel->altura_px && $size[1] !== (int) $painel->altura_px) {
                                            $fail("A altura da imagem ({$size[1]}px) não bate com o exigido ({$painel->altura_px}px).");
                                        }
                                    }
                                } 
                                elseif (str_starts_with($mimeType, 'video/')) {
                                    $getID3 = new \getID3();
                                    $fileInfo = $getID3->analyze($filePath);

                                    if (!isset($fileInfo['video']['resolution_x']) || !isset($fileInfo['video']['resolution_y'])) {
                                        $fail("Não foi possível ler as dimensões do vídeo.");
                                        return;
                                    }

                                    $width = (int) $fileInfo['video']['resolution_x'];
                                    $height = (int) $fileInfo['video']['resolution_y'];
                                    $duration = isset($fileInfo['playtime_seconds']) ? (float) $fileInfo['playtime_seconds'] : 0;

                                    if ($painel->largura_px && $width !== (int) $painel->largura_px) {
                                        $fail("A largura do vídeo ({$width}px) difere do painel ({$painel->largura_px}px).");
                                    }
                                    if ($painel->altura_px && $height !== (int) $painel->altura_px) {
                                        $fail("A altura do vídeo ({$height}px) difere do painel ({$painel->altura_px}px).");
                                    }
                                    if ($painel->tempo_maximo && abs($duration - (float) $painel->tempo_maximo) > 0.5) {
                                        $durationRound = round($duration, 1);
                                        $fail("O tempo do vídeo ({$durationRound}s) difere do exigido ({$painel->tempo_maximo}s).");
                                    }
                                }
                            },
                        ])
                        ->afterStateUpdated(function ($state, Get $get, Set $set) {
                            $painelId = $get('inventario_id');
                            
                            if (!$painelId) {
                                $set('file_path', null);
                                Notification::make()
                                    ->title('Atenção')
                                    ->warning()
                                    ->body('Selecione a Localização primeiro.')
                                    ->send();
                                return;
                            }

                            if (!$state instanceof TemporaryUploadedFile) return;

                            $painel = Inventario::find($painelId);
                            if (!$painel) return;

                            $filePath = $state->getRealPath();
                            $mimeType = $state->getMimeType();
                            $erro = null;

                            if (str_starts_with($mimeType, 'image/')) {
                                $size = @getimagesize($filePath);
                                if ($size) {
                                    if ($painel->largura_px && $size[0] !== (int) $painel->largura_px) {
                                        $erro = "A largura do seu arquivo é de **{$size[0]}px**.";
                                    } elseif ($painel->altura_px && $size[1] !== (int) $painel->altura_px) {
                                        $erro = "A altura do seu arquivo é de **{$size[1]}px**.";
                                    }
                                }
                            } elseif (str_starts_with($mimeType, 'video/')) {
                                $getID3 = new \getID3();
                                $fileInfo = $getID3->analyze($filePath);

                                if (!isset($fileInfo['video']['resolution_x']) || !isset($fileInfo['video']['resolution_y'])) {
                                    $erro = "Não foi possível ler as dimensões deste vídeo.";
                                } else {
                                    $width = (int) $fileInfo['video']['resolution_x'];
                                    $height = (int) $fileInfo['video']['resolution_y'];
                                    $duration = isset($fileInfo['playtime_seconds']) ? (float) $fileInfo['playtime_seconds'] : 0;

                                    if ($painel->largura_px && $width !== (int) $painel->largura_px) {
                                        $erro = "A largura do seu vídeo é de **{$width}px**.";
                                    } elseif ($painel->altura_px && $height !== (int) $painel->altura_px) {
                                        $erro = "A altura do seu vídeo é de **{$height}px**.";
                                    } elseif ($painel->tempo_maximo && abs($duration - (float) $painel->tempo_maximo) > 0.5) {
                                        $durationRound = round($duration, 1);
                                        $erro = "O tempo do seu vídeo é de **{$durationRound}s**.";
                                    }
                                }
                            }

                            if ($erro) {
                                $set('file_path', null); 
                                $regrasPainel = "📏 **Padrão Exigido pelo Painel:**\n"
                                              . "• Largura: **{$painel->largura_px}px**\n"
                                              . "• Altura: **{$painel->altura_px}px**\n";
                                              
                                if ($painel->tempo_maximo) {
                                    $regrasPainel .= "• Tempo Máximo: **{$painel->tempo_maximo}s**";
                                }

                                $set('painel_info', "❌ **ARQUIVO REJEITADO:** $erro \n\nPor favor, envie um arquivo com as medidas exatas:\n{$painel->largura_px}x{$painel->altura_px}px");
                                
                                Notification::make()
                                    ->title('🚫 Criativo Incompatível!')
                                    ->danger()
                                    ->body($erro . "\n\n" . $regrasPainel)
                                    ->persistent()
                                    ->send();
                                    
                                throw ValidationException::withMessages([
                                    'data.file_path' => 'O arquivo enviado não atende às especificações do painel.',
                                ]);
                            } else {
                                $info = "✅ Arquivo validado com sucesso! \n";
                                $specs = [];
                                if ($painel->largura_px && $painel->altura_px) {
                                    $specs[] = "{$painel->largura_px}x{$painel->altura_px}px";
                                }
                                if ($painel->tempo_maximo) {
                                    $specs[] = "{$painel->tempo_maximo}s";
                                }
                                $info .= implode(' | ', $specs);
                                $set('painel_info', $info);
                            }
                        }),

                    Placeholder::make('info_display')
                        ->label('')
                        ->content(fn (Get $get) => $get('painel_info') 
                            ? new HtmlString("<div style='padding: 12px; background: " . (str_contains($get('painel_info'), '❌') ? '#fee2e2' : '#dbeafe') . "; border: 1px solid " . (str_contains($get('painel_info'), '❌') ? '#fca5a5' : '#93c5fd') . "; border-radius: 6px; color: " . (str_contains($get('painel_info'), '❌') ? '#991b1b' : '#1e40af') . "; font-size: 14px; white-space: pre-line;'>" . str_replace(['**', '*'], ['<b>', '</b>'], $get('painel_info')) . "</div>")
                            : new HtmlString("<div style='padding: 12px; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 6px; color: #6b7280; font-size: 14px;'>Selecione o canal e o endereço para ver as especificações técnicas</div>")
                        )
                        ->hidden(fn (Get $get) => !$get('inventario_id')),
                ])
                ->columns(1),

            Section::make('Informações da Campanha')
                ->description('Dados do cliente e período de veiculação')
                ->schema([
                    TextInput::make('client_name')
                        ->label('Cliente / Anunciante')
                        ->required()
                        ->maxLength(255),
                    
                    TextInput::make('pi_number')
                        ->label('Número da PI')
                        ->maxLength(100)
                        ->placeholder('Opcional'),
                    
                    DatePicker::make('start_date')
                        ->label('Data de Início')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),
                    
                    DatePicker::make('end_date')
                        ->label('Data de Término')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required()
                        ->after('start_date'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('protocol_id')->label('Protocolo')->weight('bold'),
                Tables\Columns\TextColumn::make('client_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('inventario.codigo')->label('Painel'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'pending', 'pendente' => 'warning',
                        'approved', 'aprovado' => 'success',
                        'rejected', 'reprovado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (strtolower($state)) {
                        'pending', 'pendente' => 'Pendente',
                        'approved', 'aprovado' => 'Aprovado',
                        'rejected', 'reprovado' => 'Reprovado',
                        default => ucfirst($state),
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview Ao Vivo')
                    ->icon('heroicon-o-presentation-chart-bar')
                    ->color('success')
                    ->url(fn($record) => "/preview/{$record->id}")
                    ->openUrlInNewTab(),

                Action::make('download')
                    ->label('Baixar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->file_path)),
                    
                DeleteAction::make()
                    ->label('Excluir')
                    ->modalHeading('Excluir Envio')
                    ->modalDescription('Tem certeza que deseja excluir este arquivo? Esta ação não pode ser desfeita.'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnvioMidias::route('/'),
            'create' => Pages\CreateEnvioMidia::route('/create'),
        ];
    }
}