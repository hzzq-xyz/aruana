<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Adiciona colunas se não existirem
if (!Schema::hasColumn('external_medias', 'period')) {
    Schema::table('external_medias', function (Blueprint $table) {
        $table->string('period')->nullable()->after('client_name');
        $table->string('pi_number')->nullable()->after('period');
        $table->string('panel_config')->nullable()->after('pi_number');
        $table->string('file_url')->nullable()->after('file_path');
    });
    echo "✅ Colunas adicionadas com sucesso!";
} else {
    echo "⚠️ Colunas já existem!";
}