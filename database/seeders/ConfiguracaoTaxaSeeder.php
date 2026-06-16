<?php

namespace Database\Seeders;

use App\Models\ConfiguracaoTaxa;
use Illuminate\Database\Seeder;

class ConfiguracaoTaxaSeeder extends Seeder
{
    /**
     * Popula a tabela com a configuração de taxa padrão inicial.
     * Taxa fixa: R$ 25,00 | Excedente: R$ 2,00/m³
     */
    public function run(): void
    {
        ConfiguracaoTaxa::create([
            'taxa_fixa'       => 25.00,
            'valor_excedente' => 2.00,
        ]);
    }
}
