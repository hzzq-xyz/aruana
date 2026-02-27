<?php

namespace App\Filament\Cliente\Resources\EnvioMidias\Pages;

use App\Filament\Cliente\Resources\EnvioMidias\EnvioMidiaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateEnvioMidia extends CreateRecord
{
    protected static string $resource = EnvioMidiaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Vincula automaticamente ao cliente logado
        $data['user_id'] = auth()->id();

        // 2. Gera o Protocolo único (Ex: NELA-2026-ABC123)
        $data['protocol_id'] = 'NELA-' . date('Y') . '-' . strtoupper(Str::random(6));

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}