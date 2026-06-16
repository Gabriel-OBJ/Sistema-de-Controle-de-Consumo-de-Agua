<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fatura extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'faturas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'leitura_id',
        'consumidor_id',
        'valor_total',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'valor_total' => 'decimal:2',
    ];

    /**
     * Uma fatura pertence a um consumidor.
     */
    public function consumidor(): BelongsTo
    {
        return $this->belongsTo(Consumidor::class);
    }

    /**
     * Uma fatura pertence a uma leitura.
     */
    public function leitura(): BelongsTo
    {
        return $this->belongsTo(Leitura::class);
    }

    /**
     * Verifica se a fatura está paga.
     */
    public function isPaga(): bool
    {
        return $this->status === 'pago';
    }

    /**
     * Verifica se a fatura está pendente.
     */
    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }
}
