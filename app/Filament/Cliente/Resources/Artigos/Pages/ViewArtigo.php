<?php

namespace App\Filament\Cliente\Resources\Artigos\Pages;

use App\Filament\Cliente\Resources\Artigos\ArtigoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewArtigo extends ViewRecord
{
    protected static string $resource = ArtigoResource::class;

    // CORREÇÃO: Removido o 'static' para bater com a classe pai
    protected string | Width | null $maxContentWidth = Width::Full;

    public function getTitle(): string { return ''; }

    protected function getHeaderActions(): array { return []; }
}