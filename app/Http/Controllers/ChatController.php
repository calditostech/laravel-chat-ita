<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atendimento;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function enviar(Request $request)
    {
        $mensagem = $request->input('mensagem');

        switch ($mensagem) {
            case '1':
                $resposta = "📌 Serviços disponíveis:\n1️⃣ Consultas\n2️⃣ Agendamentos\n3️⃣ Voltar";
                break;

            case '2':
                $resposta = "📋 Tipos de consulta:\n1️⃣ Consulta Médica\n2️⃣ Consulta Psicológica\n3️⃣ Voltar";
                break;

            case '1.1':
                $resposta = "🩺 Especialidades Médicas:\n1️⃣ Clínica Geral\n2️⃣ Dermatologia\n3️⃣ Voltar";
                break;

            default:
                $resposta = "👋 Bem-vindo! Escolha uma opção:\n1️⃣ Serviços\n2️⃣ Consultas\n3️⃣ Contato";
        }

        return response()->json(['resposta' => $resposta]);
    }

    public function salvarAtendimento(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'cpf' => 'required|string',
            'servico_realizado' => 'required|string',
            'protocolo' => 'required|string',
            'data_hora' => 'required|date',
        ]);

        Atendimento::create([
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'servico_realizado' => $request->servico_realizado,
            'protocolo' => $request->protocolo,
            'data_hora' => $request->data_hora,
        ]);

        return response()->json(['success' => true]);
    }
}
