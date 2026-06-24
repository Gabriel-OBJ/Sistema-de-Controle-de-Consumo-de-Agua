<?php

namespace App\Services;

use App\Models\ConfiguracaoTaxa;
use App\Models\Fatura;
use App\Models\Leitura;

/**
 * Serviço de cobrança — orquestra a geração de faturas (SOLID: SRP / DIP).
 *
 * Delega o cálculo matemático ao FaturaCalculatorService (Inversão de Dependência):
 * não instancia dependências diretamente, recebe via constructor injection.
 */
class CobrancaService
{
    /**
     * Limite padrão do sistema (m³ incluídos na taxa fixa).
     * Definido aqui como regra de negócio da associação.
     */
    public const LIMITE_M3 = 10.0;

    public function __construct(
        private readonly FaturaCalculatorService $calculator
    ) {}

    /**
     * Calcula o valor total da fatura delegando ao FaturaCalculatorService.
     *
     * @param  float            $consumo_m3  Consumo em m³
     * @param  ConfiguracaoTaxa $config      Configuração de taxa vigente
     * @return float            Valor total em reais
     */
    public function calcularValor(float $consumo_m3, ConfiguracaoTaxa $config): float
    {
        return $this->calculator->calcular(
            consumoM3:      $consumo_m3,
            taxaFixa:       (float) $config->taxa_fixa,
            limiteM3:       self::LIMITE_M3,
            valorExcedente: (float) $config->valor_excedente
        );
    }

    /**
     * Gera (ou atualiza) a fatura associada a uma leitura.
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
