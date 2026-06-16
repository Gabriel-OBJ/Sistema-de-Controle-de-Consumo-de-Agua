<?php

namespace App\Services;

use App\Models\ConfiguracaoTaxa;
use App\Models\Fatura;
use App\Models\Leitura;

class CobrancaService
{
    /**
     * Limite de consumo isento do excedente (em m³).
     */
    private const LIMITE_TAXA_FIXA_M3 = 10.0;

    /**
     * Calcula o valor total da fatura com base no consumo e na configuração vigente.
     *
     * Regra de negócio:
     *   - Até 10 m³: cobra apenas a taxa fixa.
     *   - Acima de 10 m³: taxa fixa + (m³ excedentes × valor_excedente).
     *
     * Exemplo: 15 m³ consumidos, taxa_fixa=25, valor_excedente=2
     *   → R$ 25,00 + (5 m³ × R$ 2,00) = R$ 35,00
     *
     * @param  float              $consumo_m3   Consumo calculado (leitura_atual - leitura_anterior)
     * @param  ConfiguracaoTaxa   $config       Configuração de taxa ativa
     * @return float              Valor total em reais
     */
    public function calcularValor(float $consumo_m3, ConfiguracaoTaxa $config): float
    {
        $taxaFixa      = (float) $config->taxa_fixa;
        $valorExcedente = (float) $config->valor_excedente;

        if ($consumo_m3 <= self::LIMITE_TAXA_FIXA_M3) {
            return round($taxaFixa, 2);
        }

        $m3Excedentes = $consumo_m3 - self::LIMITE_TAXA_FIXA_M3;
        $valorTotal   = $taxaFixa + ($m3Excedentes * $valorExcedente);

        return round($valorTotal, 2);
    }

    /**
     * Gera (ou regera) a fatura associada a uma leitura.
     * Se já existir fatura pendente para a leitura, ela é atualizada.
     * Faturas pagas não são recalculadas.
     *
     * @param  Leitura $leitura  Leitura já persistida no banco
     * @return Fatura            Fatura criada ou atualizada
     */
    public function gerarFatura(Leitura $leitura): Fatura
    {
        $config     = ConfiguracaoTaxa::ativa();
        $valorTotal = $this->calcularValor((float) $leitura->consumo_m3, $config);

        return Fatura::updateOrCreate(
            ['leitura_id' => $leitura->id],
            [
                'consumidor_id' => $leitura->consumidor_id,
                'valor_total'   => $valorTotal,
                'status'        => 'pendente',
            ]
        );
    }
}
