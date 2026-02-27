<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracaoEmailResource\Pages;
use App\Models\ConfiguracaoEmail;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use UnitEnum;
use BackedEnum;

class ConfiguracaoEmailResource extends Resource
{
    protected static ?string $model = ConfiguracaoEmail::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Config. Email';
    
    protected static ?string $label = 'Configuração de Email';
    
    protected static ?string $pluralLabel = 'Configurações de Email';
    
    protected static string|UnitEnum|null $navigationGroup = 'Configurações';
    
    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Configurações')
                ->tabs([
                    // ABA 1: VISUAL
                    Tabs\Tab::make('🎨 Visual')
                        ->schema([
                            Section::make('Logo e Cores')
                                ->description('Configure a identidade visual do informativo')
                                ->schema([
                                    FileUpload::make('logo')
                                        ->label('Logo da Empresa')
                                        ->image()
                                        ->disk('public')
                                        ->directory('logos')
                                        ->imageEditor()
                                        ->maxSize(2048)
                                        ->helperText('Recomendado: 200x60px (PNG com fundo transparente)'),
                                    
                                    Toggle::make('mostrar_logo')
                                        ->label('Mostrar Logo no Email')
                                        ->default(true)
                                        ->inline(false),
                                    
                                    ColorPicker::make('cor_banner')
                                        ->label('Cor do Banner Principal')
                                        ->helperText('Cor de fundo do banner "INFORMATIVO DIGITAL"')
                                        ->default('#dc2626'),
                                    
                                    ColorPicker::make('cor_primaria')
                                        ->label('Cor Primária')
                                        ->helperText('Usada nos títulos e destaques')
                                        ->default('#dc2626'),
                                    
                                    ColorPicker::make('cor_secundaria')
                                        ->label('Cor Secundária')
                                        ->helperText('Usada nos textos secundários')
                                        ->default('#666666'),
                                ])->columns(2),
                        ]),
                    
                    // ABA 2: INFORMAÇÕES DA EMPRESA
                    Tabs\Tab::make('🏢 Empresa')
                        ->schema([
                            Section::make('Dados da Empresa')
                                ->description('Informações que aparecerão no email')
                                ->schema([
                                    TextInput::make('nome_empresa')
                                        ->label('Nome da Empresa')
                                        ->required()
                                        ->maxLength(255)
                                        ->default('NELA COMUNICAÇÃO'),
                                    
                                    TextInput::make('telefone')
                                        ->label('Telefone')
                                        ->tel()
                                        ->placeholder('(51) 99999-9999')
                                        ->maxLength(20),
                                    
                                    TextInput::make('email_contato')
                                        ->label('E-mail de Contato')
                                        ->email()
                                        ->placeholder('contato@empresa.com')
                                        ->maxLength(255),
                                    
                                    TextInput::make('site')
                                        ->label('Website')
                                        ->url()
                                        ->placeholder('https://www.empresa.com')
                                        ->maxLength(255),
                                    
                                    Textarea::make('endereco')
                                        ->label('Endereço')
                                        ->rows(2)
                                        ->placeholder('Rua Exemplo, 123 - Bairro - Cidade/UF')
                                        ->columnSpanFull(),
                                    
                                    Toggle::make('mostrar_info_contato')
                                        ->label('Mostrar Informações de Contato no Rodapé')
                                        ->default(true)
                                        ->helperText('Exibe telefone, email e site no rodapé do informativo')
                                        ->inline(false)
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),
                    
                    // ABA 3: TEXTOS
                    Tabs\Tab::make('📝 Textos')
                        ->schema([
                            Section::make('Textos do Informativo')
                                ->description('Personalize as mensagens do email')
                                ->schema([
                                    TextInput::make('texto_rodape_1')
                                        ->label('Texto do Rodapé (Linha 1)')
                                        ->required()
                                        ->maxLength(255)
                                        ->default('Este é um informativo automático gerado pelo sistema NELA')
                                        ->columnSpanFull(),
                                    
                                    TextInput::make('texto_rodape_2')
                                        ->label('Texto do Rodapé (Linha 2)')
                                        ->maxLength(255)
                                        ->default('Para mais informações, entre em contato conosco')
                                        ->columnSpanFull(),
                                    
                                    Textarea::make('mensagem_adicional')
                                        ->label('Mensagem Adicional (opcional)')
                                        ->rows(3)
                                        ->placeholder('Texto adicional que aparecerá antes do rodapé')
                                        ->helperText('Use para avisos, promoções ou mensagens especiais')
                                        ->columnSpanFull(),
                                ])->columns(1),
                        ]),
                    
                    // ABA 4: PREVIEW
                    Tabs\Tab::make('👁️ Preview')
                        ->schema([
                            Section::make('Visualização')
                                ->description('Preview de como ficará o informativo')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('preview')
                                        ->label('')
                                        ->content(fn () => view('filament.components.email-preview'))
                                        ->columnSpanFull(),
                                ])->columns(1),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditConfiguracaoEmail::route('/'),
        ];
    }
}
