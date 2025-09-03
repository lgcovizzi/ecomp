<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'cidade', 'estado', 'instituicao_id'];

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}