# 💬 Laravel Chat IA

Projeto de chatbot moderno utilizando **Laravel** e integração com a **API da OpenAI**. Simula um atendimento automatizado com opções pré-definidas, coleta de dados do usuário (nome e CPF), gera um número de atendimento aleatório, e armazena o histórico das conversas no banco de dados SQLite.

---

## ✨ Funcionalidades

- Interface de chat moderna com design responsivo
- Integração com OpenAI (ChatGPT)
- Opções guiadas de atendimento (simulação de serviços, consultas, etc.)
- Coleta de **nome** e **CPF** do usuário
- Exibição de modal com os dados após o atendimento
- Armazenamento de **histórico de atendimentos** no banco de dados
- Proteção contra excesso de requisições com cache temporário

---

## 🛠 Tecnologias Utilizadas

- **Laravel 10+**
- **PHP 8.1+**
- **SQLite** como banco de dados local
- **HTML5**, **CSS3**, e **JavaScript** para frontend
- **API da OpenAI** (GPT-3.5 ou GPT-4)

---

## 🚀 Instalação

```bash
# Clone o repositório
git clone https://github.com/calditostech/laravel-chat-ia.git
cd laravel-chat-ia

# Instale as dependências
composer install

# Copie o arquivo de ambiente e gere a chave
cp .env.example .env
php artisan key:generate

# Crie o banco de dados SQLite
touch database/database.sqlite

# Configure o .env
# No .env:
# DB_CONNECTION=sqlite
# OPENAI_API_KEY=sk-xxxxxx (sua chave da OpenAI)

# Execute as migrations
php artisan migrate

# Inicie o servidor local
php artisan serve
