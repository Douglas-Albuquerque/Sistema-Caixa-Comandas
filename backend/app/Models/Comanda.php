<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'mesa_id',
        'aberta_em',
        'fechada_em',
        'subtotal',
        'gorjeta',
        'total',
        'forma_pagamento'
    ];

    protected $table = 'comandas';

    protected $casts = [
        'aberta_em' => 'datetime',
        'fechada_em' => 'datetime',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemComanda::class);
    }
}