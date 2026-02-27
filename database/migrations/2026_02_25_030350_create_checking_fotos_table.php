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
    Schema::create('checking_fotos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventario_id')->constrained('inventarios')->onDelete('cascade');
        $table->json('fotos'); // Para armazenar múltiplas fotos: codigo (1), codigo (2)...
        $table->date('data_checking');
        $table->text('observacoes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checking_fotos');
    }
};
