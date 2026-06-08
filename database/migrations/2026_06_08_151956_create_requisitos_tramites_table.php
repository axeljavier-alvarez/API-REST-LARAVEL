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
        Schema::create('requisitos_tramites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisito_id')
            ->constrained('requisitos')
            ->cascadeOnDelete();

            $table->foreignId('tramite_id')
            ->constrained('tramites')
            ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitos_tramites');
    }
};
