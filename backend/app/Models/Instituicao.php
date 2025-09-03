<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    use HasFactory;

    protected $fillable = ['nome_longo', 'nome_curto'];

    public function enderecos()
    {
        return $this->hasMany(Endereco::class);
    }

    public function departamentos()
    {
        return $this->hasManyThrough(Departamento::class, Endereco::class);
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}