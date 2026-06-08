# UniSalas no Portainer

Este projeto ja possui `Dockerfile` e `docker-compose.yml` prontos para subir no Portainer.

## Como subir

1. No Portainer, acesse **Stacks**.
2. Clique em **Add stack**.
3. Escolha **Repository**.
4. Informe o repositorio do GitHub.
5. Use o arquivo `docker-compose.yml`.
6. Clique em **Deploy the stack**.

Depois que os containers iniciarem, acesse:

```text
http://IP_DO_SERVIDOR:8935
```

## Login inicial

```text
E-mail: l@gmail.com
Senha: 12345678
```

## O que a stack sobe

- `unisalas-app`: Laravel com PHP 8.3 e Apache.
- `unisalas-db`: MariaDB 10.11 com dados persistentes.
- Porta externa: `8935`.
- Banco inicial: importado automaticamente de `docs/database_unisalas.sql` na primeira subida.

Se a stack ja tiver sido criada antes e voce quiser recriar o banco do zero, remova o volume `unisalas_db` antes de fazer deploy novamente.
