<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
        
        // Inserir atendimentos padrão
        DB::table('atendimentos')->insert([
            ['nome' => 'NELA COMUNICAÇÃO', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'ANA KARINA', 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
