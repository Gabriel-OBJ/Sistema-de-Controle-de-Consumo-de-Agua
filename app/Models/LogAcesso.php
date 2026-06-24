<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAcesso extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consumidor_id',
        'acao',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consumidor()
    {
        // Using WithTrashed because the consumer might be soft deleted later
        return $this->belongsTo(Consumidor::class)->withTrashed();
    }
}
