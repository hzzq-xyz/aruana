<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes_email', function (Blueprint $table) {
            $table->id();
            
            // Visual
            $table->string('logo')->nullable(); // Logo da empresa
            $table->string('cor_banner')->default('#dc2626'); // Cor do banner
            $table->string('cor_primaria')->default('#dc2626'); // Cor primária
            $table->string('cor_secundaria')->default('#666666'); // Cor secundária
            
            // Textos do rodapé
            $table->string('texto_rodape_1')->default('Este é um informativo automático gerado pelo sistema NELA');
            $table->string('texto_rodape_2')->default('Para mais informações, entre em contato conosco');
            
            // Informações da empresa
            $table->string('nome_empresa')->default('NELA COMUNICAÇÃO');
            $table->string('telefone')->nullable();
            $table->string('email_contato')->nullable();
            $table->string('site')->nullable();
            $table->text('endereco')->nullable();
            
            // Configurações adicionais
            $table->boolean('mostrar_logo')->default(true);
            $table->boolean('mostrar_info_contato')->default(true);
            $table->text('mensagem_adicional')->nullable();
            
            $table->timestamps();
        });
        
        // Criar registro padrão
        DB::table('configuracoes_email')->insert([
            'nome_empresa' => 'NELA COMUNICAÇÃO',
            'cor_banner' => '#dc2626',
            'cor_primaria' => '#dc2626',
            'cor_secundaria' => '#666666',
            'texto_rodape_1' => 'Este é um informativo automático gerado pelo sistema NELA',
            'texto_rodape_2' => 'Para mais informações, entre em contato conosco',
            'mostrar_logo' => true,
            'mostrar_info_contato' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_email');
    }
};