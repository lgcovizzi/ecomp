<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('instituicao_id')->constrained('instituicaos')->onDelete('cascade');
            $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('cascade');
            $table->unique(['nome', 'endereco_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};