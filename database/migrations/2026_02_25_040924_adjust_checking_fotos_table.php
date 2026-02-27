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
    Schema::table('checking_fotos', function (Blueprint $table) {
        // Removemos a obrigatoriedade de um único id de inventário
        $table->foreignId('inventario_id')->nullable()->change();
        // Adicionamos um campo para guardar o agrupamento completo
        $table->json('relatorio_detalhado')->after('fotos')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
