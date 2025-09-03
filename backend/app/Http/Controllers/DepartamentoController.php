<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with(['instituicao', 'endereco'])
            ->withCount('registros')
            ->get();
        
        return response()->json($departamentos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'instituicao_id' => 'required|exists:instituicaos,id',
            'endereco_id' => 'required|exists:enderecos,id',
        ]);

        $request->validate([
            'nome' => 'unique:departamentos,nome,NULL,id,endereco_id,' . $request->endereco_id,
        ]);

        $departamento = Departamento::create($request->all());
        
        return response()->json($departamento, 201);
    }

    public function show(Departamento $departamento)
    {
        $departamento->load(['instituicao', 'endereco'])
            ->loadCount('registros');
        
        return response()->json($departamento);
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'instituicao_id' => 'required|exists:instituicaos,id',
            'endereco_id' => 'required|exists:enderecos,id',
        ]);

        $request->validate([
            'nome' => 'unique:departamentos,nome,' . $departamento->id . ',id,endereco_id,' . $request->endereco_id,
        ]);

        $departamento->update($request->all());
        
        return response()->json($departamento);
    }

    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
        
        return response()->json(null, 204);
    }

    public function byEndereco($enderecoId)
    {
        $departamentos = Departamento::where('endereco_id', $enderecoId)
            ->withCount('registros')
            ->get();
        
        return response()->json($departamentos);
    }

    public function byInstituicao($instituicaoId)
    {
        $departamentos = Departamento::where('instituicao_id', $instituicaoId)
            ->with(['endereco'])
            ->withCount('registros')
            ->get();
        
        return response()->json($departamentos);
    }
}