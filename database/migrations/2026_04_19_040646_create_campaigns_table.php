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
    Schema::create('campaigns', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // O Cliente dono da campanha
        $table->string('nome');
        
        // Regras de Data
        $table->date('data_inicio');
        $table->date('data_fim');
        
        // Regras de Exibição
        $table->enum('prioridade', ['filler', 'normal', 'exclusiva'])->default('normal');
        // filler: tapa-buraco (sem cliente), normal: rotação padrão, exclusiva: domina o painel
        
        $table->integer('peso_slot')->default(1); // Ex: Se for 2, aparece duas vezes mais que os outros no loop
        
        $table->enum('status', ['rascunho', 'ativa', 'pausada', 'finalizada'])->default('rascunho');
        $table->timestamps();
    });

    // Tabelas Pivot para ligar a Campanha aos Painéis e aos VTs
    Schema::create('campaign_inventario', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
        $table->foreignId('inventario_id')->constrained('inventarios')->cascadeOnDelete();
    });

    Schema::create('campaign_external_media', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
        $table->foreignId('external_media_id')->constrained('external_media')->cascadeOnDelete(); // A tabela dos teus VTs aprovados
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
