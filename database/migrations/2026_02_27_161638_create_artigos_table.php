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
        Schema::create('artigos', function (Blueprint $table) {
            $table->id();
            
            // Campos essenciais para a sua documentação
            $table->string('titulo'); // Título do manual/aviso
            $table->string('slug')->unique(); // Para links amigáveis
            $table->string('categoria')->default('Geral'); // Ex: Financeiro, Técnico, Prazos
            $table->longText('conteudo'); // Aqui entra o texto formatado (RichEditor)
            
            // Controles de visibilidade
            $table->boolean('is_ativo')->default(true); // Publicado ou rascunho
            $table->integer('ordem')->default(0); // Para você escolher qual tutorial vem primeiro
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artigos');
    }
};