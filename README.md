# UniSalas

Sistema web para reserva e gerenciamento de salas, feito em Laravel. O projeto permite que administradores cadastrem usuarios, blocos e salas, aprovem reservas e acompanhem o historico. Professores podem solicitar reservas, acompanhar pedidos e cancelar solicitacoes.

## Tecnologias

- PHP 8.2+
- Laravel 12
- MySQL ou MariaDB
- Composer
- Node.js e NPM
- Vite

## Como abrir o projeto pela primeira vez

### 1. Clonar o repositorio

```bash
git clone https://github.com/Luidcasotti/unisalas-atualiazobb.git
cd unisalas-atualiazobb
```

Se voce estiver usando o XAMPP, deixe a pasta do projeto dentro de:

```text
C:\xampp\htdocs\
```

### 2. Instalar as dependencias

```bash
composer install
npm install
```

### 3. Criar o arquivo de ambiente

Copie o arquivo de exemplo:

```bash
copy .env.example .env
```

No Linux ou macOS:

```bash
cp .env.example .env
```

Depois gere a chave da aplicacao:

```bash
php artisan key:generate
```

### 4. Configurar o banco de dados

Abra o XAMPP e inicie o Apache e o MySQL. Depois acesse o phpMyAdmin e crie um banco com o nome:

```text
reserva_salas
```

No arquivo `.env`, confira se as configuracoes estao assim:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reserva_salas
DB_USERNAME=root
DB_PASSWORD=
```

Se o seu MySQL tiver senha, coloque a senha em `DB_PASSWORD`.

Importante: o banco de dados do desenvolvedor nao vai junto para o GitHub. A pessoa que baixar o projeto precisa criar um banco vazio na propria maquina. Depois, o Laravel cria as tabelas e os usuarios iniciais usando os comandos abaixo.

### 5. Criar as tabelas e usuarios iniciais

```bash
php artisan migrate --seed
```

Esse comando cria as tabelas do banco e tambem cadastra os usuarios de teste para conseguir fazer login.

### 6. Rodar o projeto

Em um terminal, rode o Laravel:

```bash
php artisan serve
```

Em outro terminal, rode o Vite:

```bash
npm run dev
```

Depois acesse:

```text
http://127.0.0.1:8000
```

## Login de teste

Administrador:

```text
E-mail: l@gmail.com
Senha: 12345678
```

Professor:

```text
E-mail: professor@unisalas.local
Senha: 12345678
```

## Funcionalidades

- Login com perfil de administrador e professor
- Cadastro e gerenciamento de usuarios
- Cadastro e gerenciamento de blocos e salas
- Controle de salas e blocos em manutencao
- Solicitacao de reservas por professores
- Aprovacao, cancelamento e historico de reservas
- Verificacao de conflito de horarios
- Avisos e mensagens internas

## Comandos uteis

Limpar cache do Laravel:

```bash
php artisan optimize:clear
```

Rodar os testes:

```bash

php artisan test
```

Gerar os arquivos finais do frontend:

```bash
npm run build
```

## Autor

Desenvolvido por Luid Casotti.
link do video n1 https://drive.google.com/drive/home?hl=pt-br    
link do video n2 https://drive.google.com/file/d/1uIr86Z-83yYcm6SW2W9cXOUnU_RcvXvr/view?usp=drivesdk
link pith https://drive.google.com/file/d/1yBMjEVrm65vdn-6OnxqJyMwqEoQxXH_b/view?usp=sharing

