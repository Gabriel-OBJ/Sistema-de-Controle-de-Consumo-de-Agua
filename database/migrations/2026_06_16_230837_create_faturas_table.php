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
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leitura_id')->constrained('leituras')->onDelete('cascade');
            $table->foreignId('consumidor_id')->constrained('consumidores')->onDelete('cascade');
            $table->decimal('valor_total', 8, 2)->comment('Valor total calculado da fatura em reais');
            $table->enum('status', ['pendente', 'pago'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};
