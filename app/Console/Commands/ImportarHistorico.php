<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExternalMedia;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ImportarHistorico extends Command
{
    // O nome do comando que vamos rodar no terminal
    protected $signature = 'importar:historico';
    protected $description = 'Importa o histórico antigo de mídias validadas do JSON para o banco de dados';

    public function handle()
    {
        $path = base_path('../000VALIDACAO000/history.json');

        if (!File::exists($path)) {
            $this->error("O arquivo history.json não foi encontrado na raiz do projeto!");
            return;
        }

        $json = File::get($path);
        $dados = json_decode($json, true);

        if (!$dados) {
            $this->error("Erro ao ler o ficheiro JSON. Verifique se o formato está correto.");
            return;
        }

        $this->info("Iniciando a importação de " . count($dados) . " registos...");
        $bar = $this->output->createProgressBar(count($dados));

        // URL base do seu validador antigo (ajuste se necessário)
        $baseUrl = 'https://val.opecs.xyz/';

        foreach ($dados as $item) {
            // Converte a data do formato "23/02/2026 14:04" para o formato do banco de dados
            $dataAprovacao = Carbon::createFromFormat('d/m/Y H:i', $item['date'])->format('Y-m-d H:i:s');
            
            // Monta a URL completa para o view.php
            $fileUrl = $baseUrl . "view.php?v=" . $item['file'];

            // firstOrCreate garante que não cria duplicados se rodar o comando 2 vezes
            ExternalMedia::firstOrCreate(
                ['protocol_id' => (string) $item['id']], // Usamos o ID do JSON como protocolo
                [
                    'file_url'      => $fileUrl,
                    'original_name' => $item['name'],
                    'panel_config'  => $item['config_used'],
                    'approved_at'   => $dataAprovacao,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nImportação concluída com sucesso! Pode verificar no painel do Filament.");
    }
}