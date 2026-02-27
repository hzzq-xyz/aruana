<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            // Só adiciona se não existir (evita erro)
            if (!Schema::hasColumn('atendimentos', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('nome');
            }
        });
    }

    public function down(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            if (Schema::hasColumn('atendimentos', 'ativo')) {
                $table->dropColumn('ativo');
            }
        });
    }
};