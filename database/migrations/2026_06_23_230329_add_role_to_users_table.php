<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna `role` à tabela users.
     * Perfis disponíveis:
     *   - admin     → acesso total ao sistema
     *   - leiturista → acesso restrito apenas ao registro de leituras
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'leiturista'])
                  ->default('leiturista')
                  ->after('email');
        });
    }

    /**
     * Reverte a adição da coluna `role`.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
