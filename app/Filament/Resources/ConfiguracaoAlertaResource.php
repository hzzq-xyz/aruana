<?php

namespace App\Filament\Resources;

use App\Models\ConfiguracaoAlerta;
use App\Models\ConfiguracaoEmail;
use App\Filament\Resources\ConfiguracaoAlertaResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use UnitEnum;
use BackedEnum;

class ConfiguracaoAlertaResource extends Resource
{
    protected static ?string $model = ConfiguracaoAlerta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';
    
    protected static ?string $navigationLabel = 'Config. Alertas';
    
    protected static ?string $label = 'Configuração de Alerta';
    
    protected static ?string $pluralLabel = 'Configurações de Alerta';
    
    protected static string|UnitEnum|null $navigationGroup = 'Configurações';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Alertas e Fluxo')
                ->tabs([
                    Tabs\Tab::make('✅ Aprovação')
                        ->schema([
                            Section::make('Configuração de Sucesso')
                                ->description('Texto que o cliente recebe quando o VT é aprovado')
                                ->schema([
                                    TextInput::make('assunto_aprovacao')->label('Assunto do E-mail')->required(),
                                    Textarea::make('mensagem_aprovacao')->label('Mensagem de Texto')->rows(4),
                                ]),
                        ]),
                    Tabs\Tab::make('❌ Reprovação')
                        ->schema([
                            Section::make('Configuração de Recusa')
                                ->description('Texto que o cliente recebe quando o VT é rejeitado')
                                ->schema([
                                    TextInput::make('assunto_reprovacao')->label('Assunto do E-mail')->required(),
                                    Textarea::make('mensagem_reprovacao')->label('Mensagem de Texto')->rows(4),
                                ]),
                        ]),
                    Tabs\Tab::make('🔔 Alertas Admin')
                        ->schema([
                            Section::make('Notificações para Você')
                                ->schema([
                                    Toggle::make('ativar_alerta_admin')->label('Receber avisos de novos envios')->default(true),
                                    TextInput::make('email_recebimento_alerta')->label('Seu E-mail de Recebimento')->email(),
                                    TextInput::make('assunto_alerta_admin')->label('Assunto do Alerta'),
                                ])->columns(2),
                        ]),
                    
                    // ABA DE PREVIEW COM O FIX DO $get
                    Tabs\Tab::make('👁️ Preview')
                        ->schema([
                            Section::make('Visualização dos Alertas')
                                ->description('Veja como o cliente receberá as notificações')
                                ->schema([
                                    Placeholder::make('preview_alerta')
                                        ->label('')
                                        // PASSANDO O $get PARA A VIEW AQUI
                                        ->content(fn ($get) => view('filament.components.alerta-preview', ['get' => $get]))
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])->columnSpanFull(),
        ]);
    }
    
    public static function getPages(): array {
        return [
            'index' => Pages\EditConfiguracaoAlerta::route('/'),
        ];
    }
}