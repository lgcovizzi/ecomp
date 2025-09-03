<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\RegistroController;

Route::middleware('api')->group(function () {
    // Rotas para Instituições
    Route::apiResource('instituicoes', InstituicaoController::class)->parameters([
        'instituicoes' => 'instituicao'
    ]);
    
    // Rotas para Endereços
    Route::apiResource('enderecos', EnderecoController::class)->parameters([
        'enderecos' => 'endereco'
    ]);
    Route::get('enderecos/instituicao/{instituicaoId}', [EnderecoController::class, 'byInstituicao']);
    
    // Rotas para Departamentos
    Route::apiResource('departamentos', DepartamentoController::class)->parameters([
        'departamentos' => 'departamento'
    ]);
    Route::get('departamentos/endereco/{enderecoId}', [DepartamentoController::class, 'byEndereco']);
    Route::get('departamentos/instituicao/{instituicaoId}', [DepartamentoController::class, 'byInstituicao']);
    
    // Rotas para Registros
    Route::apiResource('registros', RegistroController::class)->parameters([
        'registros' => 'registro'
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');