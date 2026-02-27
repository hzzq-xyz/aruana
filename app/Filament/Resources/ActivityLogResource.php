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
                    ->sortable()
                    ->toggleable(),
                
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
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Deletado',
                        default => $state,
                    }),
                
                TextColumn::make('subject_type')
                    ->label('Módulo')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : 'N/A')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('subject_id')
                    ->label('ID Registro')
                    ->sortable(),
                
                TextColumn::make('causer.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->default('Sistema')
                    ->weight('medium'),
                
                TextColumn::make('changes')
                    ->label('Alterações')
                    ->formatStateUsing(function ($record) {
                        $properties = $record->properties ?? [];
                        
                        if (empty($properties)) {
                            return 'Nenhum detalhe';
                        }
                        
                        $changes = [];
                        
                        // Para updates: mostrar o que mudou
                        if (isset($properties['old']) && isset($properties['attributes'])) {
                            foreach ($properties['attributes'] as $key => $newValue) {
                                if (isset($properties['old'][$key]) && $properties['old'][$key] != $newValue) {
                                    $oldVal = is_bool($properties['old'][$key]) 
                                        ? ($properties['old'][$key] ? 'Sim' : 'Não')
                                        : (is_null($properties['old'][$key]) ? '(vazio)' : $properties['old'][$key]);
                                    $newVal = is_bool($newValue) 
                                        ? ($newValue ? 'Sim' : 'Não')
                                        : (is_null($newValue) ? '(vazio)' : $newValue);
                                    
                                    // Limitar tamanho
                                    $oldVal = strlen($oldVal) > 30 ? substr($oldVal, 0, 27) . '...' : $oldVal;
                                    $newVal = strlen($newVal) > 30 ? substr($newVal, 0, 27) . '...' : $newVal;
                                    
                                    $field = ucfirst(str_replace('_', ' ', $key));
                                    $changes[] = "{$field}: {$oldVal} → {$newVal}";
                                }
                            }
                            
                            if (empty($changes)) {
                                return 'Nenhuma alteração';
                            }
                            
                            $count = count($changes);
                            
                            // Mostrar apenas os primeiros 2
                            if ($count <= 2) {
                                return implode(' | ', $changes);
                            } else {
                                $preview = implode(' | ', array_slice($changes, 0, 2));
                                return $preview . " (+{$count} campos)";
                            }
                        }
                        
                        // Para created: mostrar campos preenchidos
                        if (isset($properties['attributes'])) {
                            $count = count($properties['attributes']);
                            return "{$count} campos preenchidos";
                        }
                        
                        // Para deleted: mostrar campos deletados
                        if (isset($properties['old'])) {
                            $count = count($properties['old']);
                            return "{$count} campos deletados";
                        }
                        
                        return '-';
                    })
                    ->wrap()
                    ->limit(100),
                
                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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