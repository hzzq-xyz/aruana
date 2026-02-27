<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GerenciarMidiaResource\Pages;
use App\Models\ExternalMedia;
use App\Models\ConfiguracaoAlerta;
use App\Models\ConfiguracaoEmail;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action; 
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class GerenciarMidiaResource extends Resource
{
    protected static ?string $model = ExternalMedia::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Aprovação de VTs';
    
    protected static ?string $label = 'Mídia';
    
    protected static ?string $pluralLabel = 'Mídias para Aprovação';
    
    protected static string|UnitEnum|null $navigationGroup = 'Mídia Externa';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('protocol_id')
                    ->label('Protocolo')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Enviado por')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inventario.codigo')
                    ->label('Canal')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fim')
                    ->date('d/m/Y')
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('file_path')
                    ->label('Arquivo')
                    ->formatStateUsing(fn ($state) => basename($state))
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true), 

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'aprovado' => 'Aprovado',
                        'reprovado' => 'Reprovado',
                    ]),

                Tables\Filters\SelectFilter::make('inventario_id')
                    ->label('Canal')
                    ->relationship('inventario', 'codigo'),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Ver')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->url(fn($record) => "/preview/{$record->id}")
                    ->openUrlInNewTab(),

                // --- AÇÃO DE APROVAR DINÂMICA ---
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprovar Mídia')
                    ->modalDescription('Tem certeza que deseja aprovar este VT para veiculação? O e-mail configurado será enviado ao cliente.')
                    ->action(function (ExternalMedia $record) {
                        $record->update(['status' => 'aprovado']);
                        
                        // Busca configurações de alerta e visuais
                        $configAlerta = ConfiguracaoAlerta::getConfig();
                        $configVisual = ConfiguracaoEmail::getConfig();
                        $record->load(['user', 'inventario']);

                        if ($record->user && $record->user->email) {
                            try {
                                $cor = $configVisual->cor_primaria ?? '#10b981';
                                $logoUrl = $configVisual->logo ? asset('storage/' . $configVisual->logo) : null;

                                $html = "
                                    <div style='font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;'>
                                        <div style='background-color: #10b981; padding: 20px; text-align: center; color: white;'>
                                            <h2 style='margin: 0;'>{$configAlerta->assunto_aprovacao}</h2>
                                        </div>
                                        <div style='padding: 20px;'>
                                            " . ($logoUrl ? "<img src='{$logoUrl}' style='max-height: 50px; margin-bottom: 20px;'>" : "") . "
                                            <p>Olá, <strong>{$record->user->name}</strong>!</p>
                                            <p style='white-space: pre-line;'>{$configAlerta->mensagem_aprovacao}</p>
                                            <div style='background-color: #f9fafb; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #10b981;'>
                                                <p style='margin: 5px 0;'><strong>Protocolo:</strong> {$record->protocol_id}</p>
                                                <p style='margin: 5px 0;'><strong>Painel:</strong> " . ($record->inventario->codigo ?? 'N/A') . "</p>
                                            </div>
                                            <p style='font-size: 12px; color: #6b7280;'>Mensagem enviada automaticamente pelo sistema NELA.</p>
                                        </div>
                                    </div>";

                                Mail::html($html, function ($message) use ($record, $configAlerta) {
                                    $message->to($record->user->email)
                                            ->subject($configAlerta->assunto_aprovacao . " - {$record->client_name}");
                                });

                                Notification::make()->title('✅ Sucesso!')->body('VT aprovado e e-mail enviado ao cliente.')->success()->send();
                            } catch (\Exception $e) {
                                Notification::make()->title('❌ Erro no envio de e-mail')->body($e->getMessage())->danger()->persistent()->send();
                            }
                        }
                    })
                    ->visible(fn (ExternalMedia $record) => in_array(strtolower($record->status), ['pending', 'pendente', 'rejected', 'reprovado'])),

                // --- AÇÃO DE REPROVAR DINÂMICA ---
                Action::make('rejeitar')
                    ->label('Reprovar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reprovar Mídia')
                    ->modalDescription('Tem certeza que deseja reprovar este VT? O e-mail de correção será enviado ao cliente.')
                    ->action(function (ExternalMedia $record) {
                        $record->update(['status' => 'reprovado']);

                        // Busca configurações de alerta e visuais
                        $configAlerta = ConfiguracaoAlerta::getConfig();
                        $configVisual = ConfiguracaoEmail::getConfig();
                        $record->load(['user', 'inventario']);

                        if ($record->user && $record->user->email) {
                            try {
                                $logoUrl = $configVisual->logo ? asset('storage/' . $configVisual->logo) : null;

                                $html = "
                                    <div style='font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;'>
                                        <div style='background-color: #ef4444; padding: 20px; text-align: center; color: white;'>
                                            <h2 style='margin: 0;'>{$configAlerta->assunto_reprovacao}</h2>
                                        </div>
                                        <div style='padding: 20px;'>
                                            " . ($logoUrl ? "<img src='{$logoUrl}' style='max-height: 50px; margin-bottom: 20px;'>" : "") . "
                                            <p>Olá, <strong>{$record->user->name}</strong>.</p>
                                            <p style='white-space: pre-line;'>{$configAlerta->mensagem_reprovacao}</p>
                                            <div style='background-color: #f9fafb; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #ef4444;'>
                                                <p style='margin: 5px 0;'><strong>Protocolo:</strong> {$record->protocol_id}</p>
                                                <p style='margin: 5px 0;'><strong>Painel:</strong> " . ($record->inventario->codigo ?? 'N/A') . "</p>
                                            </div>
                                            <p>Por favor, acesse o painel para reenviar o arquivo corrigido.</p>
                                        </div>
                                    </div>";

                                Mail::html($html, function ($message) use ($record, $configAlerta) {
                                    $message->to($record->user->email)
                                            ->subject($configAlerta->assunto_reprovacao . " - {$record->client_name}");
                                });

                                Notification::make()->title('⚠️ VT Reprovado')->body('O cliente foi notificado sobre a necessidade de ajustes.')->warning()->send();
                            } catch (\Exception $e) {
                                Notification::make()->title('❌ Erro no envio de e-mail')->body($e->getMessage())->danger()->persistent()->send();
                            }
                        }
                    })
                    ->visible(fn (ExternalMedia $record) => in_array(strtolower($record->status), ['pending', 'pendente', 'approved', 'aprovado'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGerenciarMidias::route('/'),
            'edit' => Pages\EditGerenciarMidia::route('/{record}/edit'),
        ];
    }
}