<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('artigos', function (Blueprint $table) {
        // 'cliente' será o padrão para não esconder tudo de uma vez
        $table->string('visibilidade')->default('cliente')->after('pdf_path');
    });
}

public function down(): void
{
    Schema::table('artigos', function (Blueprint $table) {
        $table->dropColumn('visibilidade');
    });
}
};
