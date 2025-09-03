<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index()
    {
        $enderecos = Endereco::with(['instituicao', 'departamentos'])
            ->withCount('registros')
            ->get();
        
        return response()->json($enderecos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'instituicao_id' => 'required|exists:instituicaos,id',
        ]);

        $request->validate([
            'titulo' => 'unique:enderecos,titulo,NULL,id,instituicao_id,' . $request->instituicao_id,
        ]);

        $endereco = Endereco::create($request->all());
        
        return response()->json($endereco, 201);
    }

    public function show(Endereco $endereco)
    {
        $endereco->load(['instituicao', 'departamentos'])
            ->loadCount('registros');
        
        return response()->json($endereco);
    }

    public function update(Request $request, Endereco $endereco)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'instituicao_id' => 'required|exists:instituicaos,id',
        ]);

        $request->validate([
            'titulo' => 'unique:enderecos,titulo,' . $endereco->id . ',id,instituicao_id,' . $request->instituicao_id,
        ]);

        $endereco->update($request->all());
        
        return response()->json($endereco);
    }

    public function destroy(Endereco $endereco)
    {
        $endereco->delete();
        
        return response()->json(null, 204);
    }

    public function byInstituicao($instituicaoId)
    {
        $enderecos = Endereco::where('instituicao_id', $instituicaoId)
            ->withCount('registros')
            ->get();
        
        return response()->json($enderecos);
    }
}