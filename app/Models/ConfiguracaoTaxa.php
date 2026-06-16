<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoTaxa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'configuracoes_taxa';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'taxa_fixa',
        'valor_excedente',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'taxa_fixa'       => 'decimal:2',
        'valor_excedente' => 'decimal:2',
    ];

    /**
     * Retorna a configuração de taxa ativa (última registrada).
     * Centraliza o acesso à taxa vigente em todo o sistema.
     */
    public static function ativa(): self
    {
        return static::latest()->firstOrFail();
    }
}
