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
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 5, 2);
            $table->decimal('humidity', 5, 2);
            $table->boolean('power_status')->default(true);
            $table->decimal('voltage', 6, 2)->nullable();
            $table->string('location')->default('Server Room');
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index('recorded_at');

            // 🔹 Tambahan kolom untuk data dari PZEM
            $table->decimal('current', 6, 2)->nullable();
            $table->decimal('power', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
