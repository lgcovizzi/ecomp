<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('cidade');
            $table->string('estado');
            $table->foreignId('instituicao_id')->constrained('instituicaos')->onDelete('cascade');
            $table->unique(['titulo', 'instituicao_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};