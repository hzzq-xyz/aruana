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
        Schema::create('play_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inventario_id')->constrained('inventarios');
    $table->foreignId('external_media_id')->constrained('external_media');
    $table->foreignId('campaign_id')->constrained('campaigns');
    $table->timestamp('played_at');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_logs');
    }
};
