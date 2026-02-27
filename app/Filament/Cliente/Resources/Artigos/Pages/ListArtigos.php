<?php

namespace App\Filament\Cliente\Resources\Artigos\Pages; // AJUSTADO PARA PLURAL

use App\Filament\Cliente\Resources\Artigos\ArtigoResource; // AJUSTADO PARA PLURAL
use Filament\Resources\Pages\ListRecords;

class ListArtigos extends ListRecords
{
    protected static string $resource = ArtigoResource::class;
}