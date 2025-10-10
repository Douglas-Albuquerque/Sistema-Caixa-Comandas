<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comanda;
use App\Models\Mesa;
use App\Models\ItemComanda;

class ComandaController extends Controller
{
    // Salvar uma nova comanda (fechar mesa)
    public function store(Request $request)
    {
        $request->validate([
            'mesa_numero' => 'required|integer',
            'itens' => 'required|array',
            'subtotal' => 'required|numeric',
            'gorjeta' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        // Cria ou encontra a mesa
        $mesa = Mesa::firstOrCreate(
            ['numero' => $request->mesa_numero],
            ['status' => 'aberta']
        );

        // Cria a comanda
        $comanda = Comanda::create([
            'mesa_id' => $mesa->id,
            'aberta_em' => now(),
            'fechada_em' => now(),
            'subtotal' => $request->subtotal,
            'gorjeta' => $request->gorjeta,
            'total' => $request->total,
            'forma_pagamento' => $request->forma_pagamento ?? null,
        ]);

        // Salva os itens
        foreach ($request->itens as $item) {
            ItemComanda::create([
                'comanda_id' => $comanda->id,
                'nome' => $item['nome'],
                'preco_unitario' => $item['preco'],
                'quantidade' => $item['quantidade'],
                'observacao' => $item['observacao'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Comanda salva com sucesso!',
            'comanda_id' => $comanda->id
        ], 201);
    }

    // Listar comandas do dia
    public function index()
    {
        $comandas = Comanda::with(['mesa', 'itens'])
            ->whereDate('fechada_em', today())
            ->orderBy('fechada_em', 'desc')
            ->get();

        return response()->json($comandas);
    }
    // Salvar pedido de cozinha (mesa permanece aberta)

    public function pedidoCozinha(Request $request)
    {
        // Validação
        $request->validate([
            'mesa_numero' => 'required|integer|min:1|exists:mesas,numero',
            'itens' => 'required|array|min:1',
            'itens.*.nome' => 'required|string',
            'itens.*.preco' => 'required|numeric|min:0',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        // Verifica se a mesa existe e está FECHADA (disponível)
        $mesa = Mesa::where('numero', $request->mesa_numero)
            ->where('status', 'fechada')
            ->first();

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa não disponível. Verifique se já está em uso ou não existe.'
            ], 400);
        }

        // Abre a mesa (muda status para "aberta")
        $mesa->status = 'aberta';
        $mesa->save();

        // Cria a comanda
        $comanda = Comanda::create([
            'mesa_id' => $mesa->id,
            'aberta_em' => now(),
            'fechada_em' => null,
            'subtotal' => 0,
            'gorjeta' => 0,
            'total' => 0,
            'forma_pagamento' => null,
        ]);

        // Salva os itens UM POR UM
        foreach ($request->itens as $itemData) {
            ItemComanda::create([
                'comanda_id' => $comanda->id,
                'nome' => $itemData['nome'],
                'preco_unitario' => $itemData['preco'],
                'quantidade' => $itemData['quantidade'],
                'observacao' => $itemData['observacao'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Pedido enviado para cozinha!',
            'comanda_id' => $comanda->id,
            'mesa_numero' => $mesa->numero,
            'itens_salvos' => count($request->itens)
        ], 201);
    }
    public function mesasDisponiveis()
    {
        $mesas = \App\Models\Mesa::where('status', 'fechada')
            ->orderBy('numero')
            ->get(['id', 'numero']);

        return response()->json($mesas);
    }
}
