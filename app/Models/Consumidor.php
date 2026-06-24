<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class Consumidor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'consumidores';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nome',
        'endereco',
        'telefone',
        'numero_medidor',
    ];

    /**
     * Um consumidor possui muitas leituras.
     */
    public function leituras(): HasMany
    {
        return $this->hasMany(Leitura::class);
    }

    /**
     * Um consumidor possui muitas faturas.
     */
    public function faturas(): HasMany
    {
        return $this->hasMany(Fatura::class);
    }
}
