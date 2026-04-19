<?php

namespace App\Filament\Resources\PlayLogResource\Widgets;

use App\Models\PlayLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\PlayLogResource\Pages\ListPlayLogs;

class PlayLogStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListPlayLogs::class;
    }

    protected function getStats(): array
    {
        // Obtém a query da tabela filtrada
        $query = $this->getPageTableQuery();

        return [
            Stat::make('Total de Exibições', $query->count())
                ->description('No período selecionado')
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('Painéis Ativos', $query->distinct('inventario_id')->count('inventario_id'))
                ->description('Recebendo conteúdo')
                ->color('info'),

            Stat::make('Campanhas no Ar', $query->distinct('campaign_id')->count('campaign_id'))
                ->description('Sendo auditadas')
                ->color('warning'),
        ];
    }
}