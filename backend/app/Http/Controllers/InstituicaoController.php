<?php

namespace App\Http\Controllers;

use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class InstituicaoController extends Controller
{
    public function index()
    {
        Log::info('Acessando o método index de InstituicaoController');
        try {
            $instituicoes = Instituicao::with(['enderecos.departamentos'])
                ->withCount('registros')
                ->get();
            Log::info('Instituições carregadas com sucesso');
            return response()->json($instituicoes);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar instituições: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_longo' => 'required|string|max:255|unique:instituicaos',
            'nome_curto' => 'required|string|max:255|unique:instituicaos',
        ]);

        $instituicao = Instituicao::create($request->all());
        
        return response()->json($instituicao, 201);
    }

    public function show(Instituicao $instituicao)
    {
        $instituicao->load(['enderecos.departamentos'])
            ->loadCount('registros');
        
        return response()->json($instituicao);
    }

    public function update(Request $request, Instituicao $instituicao)
    {
        $request->validate([
            'nome_longo' => 'required|string|max:255|unique:instituicaos,nome_longo,' . $instituicao->id,
            'nome_curto' => 'required|string|max:255|unique:instituicaos,nome_curto,' . $instituicao->id,
        ]);

        $instituicao->update($request->all());
        
        return response()->json($instituicao);
    }

    public function destroy(Instituicao $instituicao)
    {
        try {
            Log::info('Tentando deletar instituição: ' . $instituicao->id);
            
            // Check if there are any related records
            $enderecosCount = $instituicao->enderecos()->count();
            $registrosCount = $instituicao->registros()->count();
            
            Log::info("Instituição {$instituicao->id} tem {$enderecosCount} endereços e {$registrosCount} registros");
            
            $instituicao->delete();
            
            Log::info('Instituição deletada com sucesso: ' . $instituicao->id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar instituição: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao deletar instituição: ' . $e->getMessage()], 500);
        }
    }
}