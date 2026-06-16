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
        Schema::create('configuracoes_taxa', function (Blueprint $table) {
            $table->id();
            $table->decimal('taxa_fixa', 8, 2)->default(25.00)->comment('Taxa fixa mensal em reais');
            $table->decimal('valor_excedente', 8, 2)->default(2.00)->comment('Valor por m³ excedente em reais');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracoes_taxa');
    }
};
