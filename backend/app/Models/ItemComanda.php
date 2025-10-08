<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemComanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'comanda_id',
        'nome',
        'preco_unitario',
        'quantidade',
        'observacao'
    ];

    protected $table = 'itens_comanda';
    
    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }
}
