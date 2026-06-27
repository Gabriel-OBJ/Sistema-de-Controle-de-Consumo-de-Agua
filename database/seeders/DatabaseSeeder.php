<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'gestor@associacao.com.br'],
            [
                'name' => 'Gestor',
                'password' => bcrypt('senha123'),
                'role' => 'admin',
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'leiturista@associacao.com.br'],
            [
                'name' => 'Leiturista',
                'password' => bcrypt('senha123'),
                'role' => 'leiturista',
            ]
        );

        $this->call([
            ConfiguracaoTaxaSeeder::class,
        ]);
    }
}
