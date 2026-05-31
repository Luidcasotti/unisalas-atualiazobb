
### 3. Configurar o Ambiente (.env)
Copie o arquivo de exemplo e configure suas credenciais do banco de dados:
```bash
cp .env.example .env
Abra o arquivo .env no VS Code e ajuste:

Plaintext
DB_DATABASE=reserva_salas
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
4. Gerar Chave da Aplicação e Criar Banco
Bash
php artisan key:generate
php artisan migrate
5. Iniciar o Servidor
Bash
php artisan serve
Acesse: http://127.0.0.1:8000

👤 Como Acessar (Login e Perfis)
O sistema possui um simulador de perfis (Admin e Professor):

Perfil Administrador: Acesse /admin/dashboard para gerenciar usuários, blocos e aprovar reservas.

Perfil Professor: Acesse /professor/painel para solicitar novas reservas e cancelar pedidos.

Alternar Perfil: Use o botão na barra lateral para trocar de perfil rapidamente.

📌 Funcionalidades Principais
[x] Cadastro de Usuários e Gestão de Permissões.

[x] Gerenciamento de Blocos e Salas.

[x] Validação de conflito de horários.

[x] Painel de Aprovação de Reservas.

[x] Opção de Desistir da reserva para Professores.

[x] Exclusão em cascata (Segurança de banco de dados).

Desenvolvido por Luid Casotti.


### 💡 Dica de Ouro:
Se você for gravar o vídeo para o seu professor ou para o portfólio, use esse README como o seu "roteiro". Comece mostrando o README e depois mostre o sistema funcionando seguindo exatamente esses tópicos.

**Quer que eu te ajude a criar uma descrição curta para você colocar na "Bio" do



























































































# 🏫 UniNorte Salas - Sistema de Gerenciamento de Reservas

O **UniNorte Salas** é uma plataforma web desenvolvida para facilitar o agendamento de salas e laboratórios em instituições de ensino. O sistema permite que administradores gerenciem a infraestrutura e que professores solicitem reservas de forma organizada, evitando conflitos de horários.

---

## 🚀 Tecnologias Utilizadas

* **Framework:** [Laravel 10+](https://laravel.com/)
* **Linguagem:** PHP 8.2+
* **Banco de Dados:** MySQL / MariaDB
* **Frontend:** Blade Templates, Bootstrap 5 e FontAwesome (ícones).

---

## 🛠️ Como Rodar o Projeto Localmente

Siga os passos abaixo para configurar o ambiente em sua máquina:

### 1. Clonar o Repositório
```bash
git clone [https://github.com/SEU_USUARIO/uninorte-salas.git](https://github.com/SEU_USUARIO/uninorte-salas.git)
cd uninorte-salas