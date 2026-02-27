<?php

namespace App\Filament\Cliente\Widgets;

use App\Models\ExternalMedia;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ClienteStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Filtramos apenas as mídias deste utilizador/agência
        $userId = auth()->id();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            Stat::make('Total de Mídias', ExternalMedia::where('user_id', $userId)->count())
                ->description('Histórico total enviado')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Enviadas este Mês', ExternalMedia::where('user_id', $userId)
                ->where('approved_at', '>=', $thisMonth)
                ->count())
                ->description('Volume em ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Última Validação', ExternalMedia::where('user_id', $userId)
                ->latest('approved_at')
                ->first()?->approved_at?->diffForHumans() ?? 'Nenhuma')
                ->description('Atividade mais recente')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}