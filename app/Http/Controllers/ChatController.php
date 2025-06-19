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
    
    // PARA INTERGRAÇÃO COM OPENAI
    public function chatOpenAI(Request $request)
    {
        $prompt = $request->input('prompt');

        if (!$prompt) {
            return response()->json(['resposta' => 'O prompt é obrigatório.'], 400);
        }

        // Verifica se já fez uma requisição recentemente (bloqueio de 5 segundos)
        if (Cache::has('openai_last_request_' . $request->ip())) {
            return response()->json([
                'resposta' => 'Você fez muitas requisições em pouco tempo. Aguarde alguns segundos.'
            ], 429);
        }

        // Salva no cache que uma requisição foi feita agora, bloqueando novas por 5 segundos
        Cache::put('openai_last_request_' . $request->ip(), true, 5);

        // Chave da API no .env (melhor prática)
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json(['resposta' => 'Chave da API não configurada.'], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            if ($response->status() == 200) {
                $choices = $response->json('choices');
                $text = $choices[0]['message']['content'] ?? 'Sem resposta da OpenAI.';

                return response()->json(['resposta' => trim($text)]);
            }

            if ($response->status() == 429) {
                return response()->json([
                    'resposta' => 'Você fez muitas requisições em pouco tempo. Aguarde um momento.'
                ], 429);
            }

            // Outros erros da API
            return response()->json([
                'resposta' => 'Erro na comunicação com a OpenAI: ' . $response->json('error.message')
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'resposta' => 'Erro ao chamar OpenAI: ' . $e->getMessage()
            ], 500);
        }
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
