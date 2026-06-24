<?php

namespace App\Services;

/**
 * Serviço de cálculo de fatura — responsabilidade única (SOLID: SRP).
 *
 * Não acessa banco de dados nem conhece models.
 * Recebe todos os parâmetros necessários via injeção (Princípio da Inversão de Dependência).
 *
 * Regra de negócio:
 *   - Consumo ≤ limiteM3  → cobra apenas a taxa fixa
 *   - Consumo > limiteM3  → taxa fixa + (m³ excedentes × valorExcedente)
 *
 * Exemplo: 15 m³, taxaFixa=25, limiteM3=10, valorExcedente=2
 *   → R$ 25,00 + (5 × R$ 2,00) = R$ 35,00
 */
class FaturaCalculatorService
{
    /**
     * Calcula o valor total da fatura.
     *
     * @param float $consumoM3       Consumo medido em metros cúbicos
     * @param float $taxaFixa        Valor fixo cobrado independente do consumo (R$)
     * @param float $limiteM3        Limite de m³ incluídos na taxa fixa
     * @param float $valorExcedente  Valor cobrado por m³ acima do limite (R$)
     * @return float                 Valor total arredondado em reais
     */
    public function calcular(
        float $consumoM3,
        float $taxaFixa,
        float $limiteM3,
        float $valorExcedente
    ): float {
        if ($consumoM3 <= $limiteM3) {
            return round($taxaFixa, 2);
        }

        $m3Excedentes = $consumoM3 - $limiteM3;
        $valorTotal   = $taxaFixa + ($m3Excedentes * $valorExcedente);

        return round($valorTotal, 2);
    }
}
