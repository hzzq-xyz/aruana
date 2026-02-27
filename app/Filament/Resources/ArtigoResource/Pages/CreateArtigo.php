<?php

namespace App\Filament\Resources\ArtigoResource\Pages;

use App\Filament\Resources\ArtigoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArtigo extends CreateRecord
{
    protected static string $resource = ArtigoResource::class;

    // Redireciona para a lista após criar
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}