<?php

namespace App\Filament\Resources\GerenciarMidiaResource\Pages;

use App\Filament\Resources\GerenciarMidiaResource;
use Filament\Resources\Pages\ListRecords;

class ListGerenciarMidias extends ListRecords
{
    protected static string $resource = GerenciarMidiaResource::class;
    
    protected static ?string $title = 'Aprovação de VTs';
}