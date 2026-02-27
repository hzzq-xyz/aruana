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
    Schema::create('external_media', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Dono da mídia
        $table->string('protocol_id')->unique(); // Protocolo NELA-
        $table->string('pi_number')->nullable(); // Opcional
        $table->string('client_name');           // Obrigatório
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->foreignId('inventario_id')->constrained('inventarios'); // Para validar medidas
        $table->string('file_path');
        $table->string('status')->default('pendente');
        $table->timestamp('approved_at')->nullable(); // Mata o erro do Dashboard
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_media');
    }
};
