<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Chat Consultas</title>
  <style>
    /* Estilo degradê roxo + azul */
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      background: linear-gradient(135deg, #6a0dad, #0047ab);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    #chat-container {
      background: #fff;
      width: 1200px;
      max-width: 95vw;
      height: 700px;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    #chat-header {
      background: #6a0dad;
      color: white;
      padding: 20px;
      font-size: 1.5rem;
      font-weight: bold;
      text-align: center;
    }
    #chat-messages {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background: #f4f4f9;
    }
    .message {
      margin-bottom: 15px;
      max-width: 80%;
      padding: 12px 18px;
      border-radius: 25px;
      line-height: 1.4;
      font-size: 1rem;
      word-wrap: break-word;
      clear: both;
    }
    .message.user {
      background: #6a0dad;
      color: white;
      float: right;
      border-bottom-right-radius: 5px;
    }
    .message.bot {
      background: #ddd;
      color: #333;
      float: left;
      border-bottom-left-radius: 5px;
    }
    #chat-input-area {
      padding: 15px 20px;
      background: #eee;
      display: flex;
      gap: 10px;
    }
    #chat-input-area input {
      flex: 1;
      padding: 10px 15px;
      border-radius: 30px;
      border: 1px solid #ccc;
      font-size: 1rem;
      outline: none;
      transition: 0.3s;
    }
    #chat-input-area input:focus {
      border-color: #6a0dad;
      box-shadow: 0 0 5px #6a0dad;
    }
    #chat-input-area button {
      background: #6a0dad;
      border: none;
      color: white;
      font-weight: bold;
      padding: 0 20px;
      border-radius: 30px;
      cursor: pointer;
      transition: 0.3s;
    }
    #chat-input-area button:hover {
      background: #5a0cbd;
    }
    /* Botões de opções */
    #options-container {
      margin-top: 15px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .option-btn {
      background: #6a0dad;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 1rem;
      transition: 0.3s;
    }
    .option-btn:hover {
      background: #5a0cbd;
    }

    /* Modal */
    #modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    #modal {
      background: white;
      padding: 30px 40px;
      border-radius: 15px;
      max-width: 400px;
      width: 90vw;
      box-shadow: 0 5px 30px rgba(0,0,0,0.4);
      text-align: center;
    }
    #modal h2 {
      margin-top: 0;
      margin-bottom: 20px;
      color: #6a0dad;
    }
    #modal p {
      margin: 10px 0;
      font-size: 1.1rem;
    }
    #modal button {
      margin-top: 25px;
      background: #6a0dad;
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 1rem;
      border-radius: 25px;
      cursor: pointer;
      transition: 0.3s;
    }
    #modal button:hover {
      background: #5a0cbd;
    }

  </style>
</head>
<body>
  <div id="chat-container">
    <div id="chat-header">Chat de Consultas</div>
    <div id="chat-messages"></div>

    <div id="chat-input-area">
      <input type="text" id="chat-input" placeholder="Digite sua mensagem aqui..." autocomplete="off" />
      <button id="send-btn">Enviar</button>
    </div>
  </div>

  <div id="modal-overlay">
    <div id="modal">
      <h2>Consulta Finalizada</h2>
      <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
      <p><strong>CPF:</strong> <span id="modal-cpf"></span></p>
      <p><strong>Serviço Realizado:</strong> <span id="modal-servico"></span></p>
      <p><strong>Protocolo:</strong> <span id="modal-protocolo"></span></p>
      <p><strong>Data/Hora:</strong> <span id="modal-datahora"></span></p>
      <button id="close-modal-btn">Fechar</button>
    </div>
  </div>

  <script>
    // Variáveis para armazenar dados do usuário e estado do chat
    let nome = '';
    let cpf = '';
    let protocolo = '';
    let servicoEscolhido = '';
    let etapa = 'pedidoNome'; // etapas: pedidoNome, pedidoCPF, menu, submenu, finalizado

    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const modalOverlay = document.getElementById('modal-overlay');

    // Função para adicionar mensagem no chat
    function addMessage(text, sender = 'bot') {
      const div = document.createElement('div');
      div.classList.add('message', sender);
      div.textContent = text;
      chatMessages.appendChild(div);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Função para limpar opções anteriores
    function clearOptions() {
      const opts = document.getElementById('options-container');
      if (opts) opts.remove();
    }

    // Função para criar opções (botões)
    function criarOpcoes(opcoes) {
      clearOptions();
      const container = document.createElement('div');
      container.id = 'options-container';
      opcoes.forEach(op => {
        const btn = document.createElement('button');
        btn.classList.add('option-btn');
        btn.textContent = op.text;
        btn.onclick = () => op.onClick();
        container.appendChild(btn);
      });
      document.getElementById('chat-container').appendChild(container);
    }

    // Função para gerar protocolo aleatório (ex: 6 dígitos)
    function gerarProtocolo() {
      return Math.floor(100000 + Math.random() * 900000).toString();
    }

    // Função para enviar dados para backend e salvar
    async function salvarAtendimento() {
      try {
        const response = await fetch('/salvar-atendimento', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            nome,
            cpf,
            servico_realizado: servicoEscolhido,
            protocolo,
            data_hora: new Date().toISOString()
          })
        });
        const data = await response.json();
        if (data.success) {
          console.log('Atendimento salvo com sucesso');
        } else {
          console.error('Erro ao salvar atendimento');
        }
      } catch (err) {
        console.error('Erro ao salvar atendimento:', err);
      }
    }

    // Função para abrir modal com os dados
    function abrirModal() {
      document.getElementById('modal-nome').textContent = nome;
      document.getElementById('modal-cpf').textContent = cpf;
      document.getElementById('modal-servico').textContent = servicoEscolhido;
      document.getElementById('modal-protocolo').textContent = protocolo;
      document.getElementById('modal-datahora').textContent = new Date().toLocaleString();
      modalOverlay.style.display = 'flex';
    }

    // Fechar modal
    document.getElementById('close-modal-btn').onclick = () => {
      modalOverlay.style.display = 'none';
      location.reload(); // Reinicia o chat para novo atendimento
    };

    // Função principal que processa a mensagem do usuário conforme etapa
    function processarMensagem(texto) {
      texto = texto.trim();

      if (etapa === 'pedidoNome') {
        if (texto.length < 3) {
          addMessage('Por favor, informe um nome válido.', 'bot');
          return;
        }
        nome = texto;
        addMessage(nome, 'user');
        addMessage('Obrigado, agora digite seu CPF:', 'bot');
        etapa = 'pedidoCPF';
        chatInput.value = '';
        return;
      }

      if (etapa === 'pedidoCPF') {
        // Validação simples de CPF (apenas 11 números)
        const cpfNumeros = texto.replace(/\D/g, '');
        if (cpfNumeros.length !== 11) {
          addMessage('CPF inválido. Digite os 11 dígitos sem pontos ou traços.', 'bot');
          return;
        }
        cpf = texto;
        addMessage(cpf, 'user');
        addMessage(`Olá ${nome}, escolha uma opção:`, 'bot');
        etapa = 'menu';
        chatInput.value = '';
        mostrarMenu();
        return;
      }

      if (etapa === 'menu') {
        // Aqui não aceita texto, só clique nos botões, então ignore texto no input
        addMessage('Por favor, selecione uma opção clicando nos botões abaixo.', 'bot');
        chatInput.value = '';
        return;
      }

      if (etapa === 'submenu') {
        addMessage('Por favor, selecione uma opção clicando nos botões abaixo.', 'bot');
        chatInput.value = '';
        return;
      }
    }

    // Função para mostrar menu principal
    function mostrarMenu() {
      criarOpcoes([
        {
          text: 'Consultas',
          onClick: () => mostrarConsultas()
        },
        {
          text: 'Serviços',
          onClick: () => mostrarServicos()
        },
        {
          text: 'Encerrar Atendimento',
          onClick: () => finalizarAtendimento()
        }
      ]);
    }

    // Submenus de consultas
    function mostrarConsultas() {
      etapa = 'submenu';
      addMessage('Escolha o tipo de consulta:', 'bot');
      criarOpcoes([
        {
          text: 'Consulta Médica',
          onClick: () => selecionarServico('Consulta Médica')
        },
        {
          text: 'Consulta Odontológica',
          onClick: () => selecionarServico('Consulta Odontológica')
        },
        {
          text: 'Voltar',
          onClick: () => {
            etapa = 'menu';
            mostrarMenu();
          }
        }
      ]);
    }

    // Submenus de serviços
    function mostrarServicos() {
      etapa = 'submenu';
      addMessage('Escolha o serviço desejado:', 'bot');
      criarOpcoes([
        {
          text: 'Agendamento',
          onClick: () => selecionarServico('Agendamento')
        },
        {
          text: 'Cancelamento',
          onClick: () => selecionarServico('Cancelamento')
        },
        {
          text: 'Voltar',
          onClick: () => {
            etapa = 'menu';
            mostrarMenu();
          }
        }
      ]);
    }

    // Quando usuário escolhe serviço ou consulta
    function selecionarServico(nomeServico) {
      servicoEscolhido = nomeServico;
      addMessage(`Você escolheu: ${nomeServico}`, 'bot');
      protocolo = gerarProtocolo();

      // Finaliza atendimento e abre modal
      etapa = 'finalizado';
      clearOptions();

      addMessage('Finalizando atendimento...', 'bot');

      // Salvar no backend
      salvarAtendimento();

      abrirModal();
    }

    // Evento botão enviar
    sendBtn.onclick = () => {
      const texto = chatInput.value;
      if (!texto) return;
      addMessage(texto, 'user');
      processarMensagem(texto);
      chatInput.value = '';
    };

    // Enter envia mensagem
    chatInput.addEventListener('keypress', e => {
      if (e.key === 'Enter') {
        sendBtn.click();
      }
    });

    // Início do chat
    addMessage('Olá! Por favor, informe seu nome:', 'bot');

  </script>
</body>
</html>
