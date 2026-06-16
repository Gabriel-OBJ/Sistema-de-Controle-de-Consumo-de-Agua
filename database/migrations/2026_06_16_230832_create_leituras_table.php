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
        Schema::create('leituras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumidor_id')->constrained('consumidores')->onDelete('cascade');
            $table->unsignedTinyInteger('mes_referencia')->comment('Mês de referência (1-12)');
            $table->unsignedSmallInteger('ano_referencia')->comment('Ano de referência (ex: 2024)');
            $table->decimal('leitura_anterior', 10, 3)->comment('Leitura do mês anterior em m³');
            $table->decimal('leitura_atual', 10, 3)->comment('Leitura do mês atual em m³');
            $table->decimal('consumo_m3', 10, 3)->comment('Consumo calculado: atual - anterior');
            $table->timestamps();

            // Garante uma única leitura por consumidor/mês/ano
            $table->unique(['consumidor_id', 'mes_referencia', 'ano_referencia'], 'leitura_unica_por_mes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leituras');
    }
};
