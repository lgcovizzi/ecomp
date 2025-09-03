<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'instituicao_id', 'endereco_id', 'departamento_id'];

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}