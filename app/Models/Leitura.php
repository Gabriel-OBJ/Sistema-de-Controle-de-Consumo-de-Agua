<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Leitura extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'leituras';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'consumidor_id',
        'mes_referencia',
        'ano_referencia',
        'leitura_anterior',
        'leitura_atual',
        'consumo_m3',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'leitura_anterior' => 'decimal:3',
        'leitura_atual'    => 'decimal:3',
        'consumo_m3'       => 'decimal:3',
    ];

    /**
     * Uma leitura pertence a um consumidor.
     */
    public function consumidor(): BelongsTo
    {
        return $this->belongsTo(Consumidor::class);
    }

    /**
     * Uma leitura possui uma fatura.
     */
    public function fatura(): HasOne
    {
        return $this->hasOne(Fatura::class);
    }

    /**
     * Retorna o nome do mês em português.
     */
    public function getNomeMesAttribute(): string
    {
        $meses = [
            1  => 'Janeiro',
            2  => 'Fevereiro',
            3  => 'Março',
            4  => 'Abril',
            5  => 'Maio',
            6  => 'Junho',
            7  => 'Julho',
            8  => 'Agosto',
            9  => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        return $meses[$this->mes_referencia] ?? 'Desconhecido';
    }
}
