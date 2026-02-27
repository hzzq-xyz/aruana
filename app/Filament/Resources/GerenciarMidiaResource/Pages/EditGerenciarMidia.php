<?php

namespace App\Filament\Resources\GerenciarMidiaResource\Pages;

use App\Filament\Resources\GerenciarMidiaResource;
use App\Models\ExternalMedia;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;

class EditGerenciarMidia extends EditRecord
{
    protected static string $resource = GerenciarMidiaResource::class;
    
    protected static ?string $title = 'Revisar Mídia';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('protocol_id')
                    ->label('Protocolo')
                    ->disabled(),

                TextInput::make('user.name')
                    ->label('Enviado por')
                    ->disabled(),

                TextInput::make('client_name')
                    ->label('Cliente')
                    ->disabled(),

                Select::make('inventario_id')
                    ->label('Canal / Painel')
                    ->relationship('inventario', 'codigo')
                    ->disabled(),

                DatePicker::make('start_date')
                    ->label('Data Início')
                    ->disabled(),

                DatePicker::make('end_date')
                    ->label('Data Fim')
                    ->disabled(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'approved' => 'Aprovado',
                        'rejected' => 'Reprovado',
                    ])
                    ->required(),

                Textarea::make('rejection_reason')
                    ->label('Motivo da Reprovação')
                    ->rows(3)
                    ->visible(fn ($get) => $get('status') === 'rejected')
                    ->helperText('Obrigatório para rejeições'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aprovar')
                ->label('Aprovar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aprovar esta mídia?')
                ->modalDescription('A mídia será marcada como aprovada e o cliente será notificado.')
                ->visible(fn (ExternalMedia $record) => $record->status === 'pending')
                ->action(function (ExternalMedia $record) {
                    $record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Mídia aprovada!')
                        ->success()
                        ->send();
                }),

            Action::make('reprovar')
                ->label('Reprovar')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reprovar esta mídia?')
                ->modalDescription('A mídia será marcada como reprovada.')
                ->visible(fn (ExternalMedia $record) => $record->status === 'pending')
                ->action(function (ExternalMedia $record) {
                    $record->update([
                        'status' => 'rejected',
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Mídia reprovada!')
                        ->warning()
                        ->send();
                }),

            Action::make('ver_arquivo')
                ->label('Ver Arquivo')
                ->icon('heroicon-o-play')
                ->color('info')
                ->url(fn (ExternalMedia $record) => asset('storage/' . $record->file_path))
                ->openUrlInNewTab(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] === 'approved') {
            $data['approved_at'] = now();
        }
        
        return $data;
    }
}