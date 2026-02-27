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
        // Só tenta adicionar se a coluna ainda NÃO existir
        if (!Schema::hasColumn('artigos', 'pdf_path')) {
            $table->string('pdf_path')->nullable()->after('categoria');
        }
        
        // Continua permitindo que o conteúdo seja nulo
        $table->text('conteudo')->nullable()->change();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artigos', function (Blueprint $table) {
            // Remove a coluna criada
            $table->dropColumn('pdf_path');
            
            // Nota: Reverter o ->change() do conteúdo é opcional, 
            // geralmente manter nullable não causa problemas.
        });
    }
};