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
    Schema::create('configuracao_alertas', function (Blueprint $table) {
        $table->id();
        // Aprovação
        $table->string('assunto_aprovacao')->default('✅ Seu VT foi aprovado!');
        $table->text('mensagem_aprovacao')->nullable();
        // Reprovação
        $table->string('assunto_reprovacao')->default('⚠️ Seu VT precisa de ajustes');
        $table->text('mensagem_reprovacao')->nullable();
        // Alertas Admin
        $table->boolean('ativar_alerta_admin')->default(true);
        $table->string('email_recebimento_alerta')->nullable();
        $table->string('assunto_alerta_admin')->default('📢 NELA: Novo material recebido');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_alertas');
    }
};
