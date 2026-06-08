# UniSalas

Sistema web para reserva e gerenciamento de salas, desenvolvido em Laravel.

O UniSalas organiza o fluxo de solicitacao, aprovacao e acompanhamento de reservas de salas. Administradores gerenciam usuarios, blocos, salas, manutencoes, avisos e aprovacoes. Professores solicitam reservas, acompanham status, consultam historico e trocam mensagens internas.

## Sumario

- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Como rodar localmente](#como-rodar-localmente)
- [Como rodar no Portainer](#como-rodar-no-portainer)
- [Login de teste](#login-de-teste)
- [Comandos uteis](#comandos-uteis)
- [Documentacao](#documentacao)
- [Links do projeto](#links-do-projeto)

## Funcionalidades

- Login com perfil de administrador e professor.
- Cadastro e gerenciamento de usuarios.
- Cadastro e gerenciamento de blocos e salas.
- Controle de salas e blocos em manutencao.
- Solicitacao de reservas por professores.
- Aprovacao, rejeicao, cancelamento e historico de reservas.
- Verificacao de conflito de sala, data e periodo.
- Reservas recorrentes.
- Avisos e mensagens internas.
- Suporte a Docker/Portainer na porta `8935`.

## Tecnologias

- PHP 8.2+
- Laravel 12
- MySQL ou MariaDB
- Composer
- Node.js e NPM
- Vite
- Docker e Docker Compose

## Como Rodar Localmente

### 1. Clonar o repositorio

```bash
git clone https://github.com/Luidcasotti/unisalas-atualiazobb.git
cd unisalas-atualiazobb
```

Se estiver usando XAMPP, deixe a pasta dentro de:

```text
C:\xampp\htdocs\
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Criar o arquivo `.env`

No Windows:

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

### 4. Configurar o banco

Inicie Apache e MySQL pelo XAMPP. Crie um banco chamado:

```text
reserva_salas
```

No `.env`, confira:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reserva_salas
DB_USERNAME=root
DB_PASSWORD=
```

Se o seu MySQL tiver senha, preencha `DB_PASSWORD`.

### 5. Criar tabelas e usuarios iniciais

```bash
php artisan migrate --seed
```

### 6. Rodar o projeto

Em um terminal:

```bash
php artisan serve
```

Em outro terminal:

```bash
npm run dev
```

Acesse:

```text
http://127.0.0.1:8000
```

## Como Rodar No Portainer

O projeto ja possui `Dockerfile` e `docker-compose.yml`.

No Portainer:

1. Acesse **Stacks**.
2. Clique em **Add stack**.
3. Escolha **Repository**.
4. Informe este repositorio.
5. Use o arquivo `docker-compose.yml`.
6. Clique em **Deploy the stack**.

Depois acesse:

```text
http://IP_DO_SERVIDOR:8935
```

Mais detalhes em [PORTAINER.md](PORTAINER.md).

## Login De Teste

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

## Comandos Uteis

Limpar cache do Laravel:

```bash
php artisan optimize:clear
```

Rodar testes:

```bash
php artisan test
```

Gerar arquivos finais do frontend:

```bash
npm run build
```

## Documentacao

Os arquivos de documentacao ficam em [docs](docs/README.md).

Principais arquivos:

- [Documentacao essencial](docs/DOCUMENTACAO_ESSENCIAL_UNISALAS.md)
- [Documentacao completa](docs/DOCUMENTACAO_COMPLETA_UNISALAS.md)
- [Script do banco](docs/database_unisalas.sql)

## Links Do Projeto

- Video N1: https://drive.google.com/drive/home?hl=pt-br
- Video N2: https://drive.google.com/file/d/1uIr86Z-83yYcm6SW2W9cXOUnU_RcvXvr/view?usp=drivesdk
- Pitch: https://drive.google.com/file/d/1yBMjEVrm65vdn-6OnxqJyMwqEoQxXH_b/view?usp=sharing

## Autor

Desenvolvido por Luid Casotti.
