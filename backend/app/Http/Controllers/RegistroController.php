<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function index()
    {
        $registros = Registro::with(['instituicao', 'endereco', 'departamento'])
            ->get();
        
        return response()->json($registros);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'instituicao_id' => 'nullable|exists:instituicaos,id',
            'endereco_id' => 'nullable|exists:enderecos,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
        ]);

        // Validar consistência dos relacionamentos
        if ($request->endereco_id && $request->instituicao_id) {
            $endereco = \App\Models\Endereco::find($request->endereco_id);
            if ($endereco && $endereco->instituicao_id != $request->instituicao_id) {
                return response()->json(['error' => 'Endereço não pertence à instituição selecionada'], 422);
            }
        }

        if ($request->departamento_id && $request->endereco_id) {
            $departamento = \App\Models\Departamento::find($request->departamento_id);
            if ($departamento && $departamento->endereco_id != $request->endereco_id) {
                return response()->json(['error' => 'Departamento não pertence ao endereço selecionado'], 422);
            }
        }

        $registro = Registro::create($request->all());
        
        return response()->json($registro, 201);
    }

    public function show(Registro $registro)
    {
        $registro->load(['instituicao', 'endereco', 'departamento']);
        
        return response()->json($registro);
    }

    public function update(Request $request, Registro $registro)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'instituicao_id' => 'nullable|exists:instituicaos,id',
            'endereco_id' => 'nullable|exists:enderecos,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
        ]);

        // Validar consistência dos relacionamentos
        if ($request->endereco_id && $request->instituicao_id) {
            $endereco = \App\Models\Endereco::find($request->endereco_id);
            if ($endereco && $endereco->instituicao_id != $request->instituicao_id) {
                return response()->json(['error' => 'Endereço não pertence à instituição selecionada'], 422);
            }
        }

        if ($request->departamento_id && $request->endereco_id) {
            $departamento = \App\Models\Departamento::find($request->departamento_id);
            if ($departamento && $departamento->endereco_id != $request->endereco_id) {
                return response()->json(['error' => 'Departamento não pertence ao endereço selecionado'], 422);
            }
        }

        $registro->update($request->all());
        
        return response()->json($registro);
    }

    public function destroy(Registro $registro)
    {
        $registro->delete();
        
        return response()->json(null, 204);
    }
}