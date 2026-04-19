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
    Schema::create('campaign_schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
        $table->integer('dia_semana'); // 0 = Domingo, 1 = Segunda... 6 = Sábado
        $table->time('hora_inicio');
        $table->time('hora_fim');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_schedules');
    }
};
