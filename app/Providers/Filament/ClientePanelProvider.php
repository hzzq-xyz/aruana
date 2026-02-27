<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Pages; // Necessário para o Dashboard padrão

class ClientePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cliente')
            ->path('cliente') // Acesso via nela.opecs.xyz/cliente
            ->login()
            ->colors([
                'primary' => Color::Blue, // Cor distinta para identificar o painel do cliente
            ])
            // --- TOQUES PROFISSIONAIS (PADRÃO FILAMENT 5) ---
            ->font('Inter') 
            ->favicon(asset('images/favicon.png'))
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            // ------------------------------------------------
            
            // O Filament precisa que estas pastas existam fisicamente no servidor
            ->discoverResources(in: app_path('Filament/Cliente/Resources'), for: 'App\\Filament\\Cliente\\Resources')
            ->discoverPages(in: app_path('Filament/Cliente/Pages'), for: 'App\\Filament\\Cliente\\Pages')
            
            ->pages([
                Pages\Dashboard::class, // Adiciona um Dashboard inicial para evitar erro 404 ao logar
            ])
            ->discoverWidgets(in: app_path('Filament/Cliente/Widgets'), for: 'App\\Filament\\Cliente\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}