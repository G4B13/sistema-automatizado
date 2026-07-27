# Sistema LUA

Versão web do **Espelho Interativo**, um projeto de lembretes visuais para
pessoas neurodiversas. Cada rotina é representada por um desenho, para que a
pessoa reconheça a tarefa antes mesmo de ler o texto.

O projeto foi concebido para três superfícies: web, mobile e um totem físico
sobre Raspberry Pi, com touchscreen e espelho de fundo falso. Este repositório
traz a versão web.

Esta é uma **refatoração** do projeto original, mantendo PHP.

---

## O que mudou na refatoração

A versão anterior não funcionava. Não era questão de organização de código:
havia falhas que impediam o uso do sistema.

| Antes | Depois |
| --- | --- |
| O login consultava `SELECT id, username, password FROM users`, mas o schema criava `name`, `email` e `password_hash`. **Nenhuma credencial jamais funcionou** | Nomes de coluna unificados entre schema e código, com login verificado em teste |
| `register.html`, `main.php`, `logout.php` e `delete-account.php` eram referenciados e **não existiam no repositório** | Todas as páginas existem e o fluxo fecha: cadastro, login, painel, logout |
| Erros de SQL eram interpolados dentro de `alert()`, expondo detalhes do banco e abrindo espaço para injeção de script | Erros tratados no servidor e exibidos como texto escapado |
| `new mysqli("localhost", "root", "")` no código | PDO com credenciais por variável de ambiente |
| Sem proteção CSRF | Token por sessão, comparado com `hash_equals()` |
| Ações de estado por `GET` | `POST` para concluir e excluir |
| Arquivos na raiz, todos alcançáveis pelo navegador | `public/` como raiz do servidor |
| `logo.svg` era um rascunho em XML com tags em português, que nunca renderizou | Logotipo real em SVG |
| Dois logotipos raster somando 267 KB, redundantes com o SVG | Removidos |
| Sem instruções para rodar | `docker compose up` |

---

## Decisões de interface

O público do projeto orienta as escolhas visuais.

- **Alvo de toque de 48 px** em todos os controles, mínimo recomendado pela
  WCAG, porque o destino é um totem operado com o dedo.
- **Ícone antes do texto.** O lembrete é identificado pelo desenho; o título
  vem depois. A escolha do ícone no formulário é uma grade de cartões
  grandes, não uma lista suspensa.
- **Sem animação.** Movimento inesperado atrapalha parte do público, então
  não há transição de entrada, e `prefers-reduced-motion` desliga o resto.
- **Uma informação por vez.** O painel mostra apenas os lembretes e um
  filtro. Sem gráficos, sem contadores decorativos.

---

## Rodando

Requisito: Docker.

```bash
git clone https://github.com/gabrielc-neto/sistema-automatizado.git
cd sistema-automatizado

docker compose up -d --build
docker compose exec app php database/seed.php   # cria conta demo com lembretes
```

Acesse **http://localhost:8081** e entre com as credenciais impressas pelo seed.
Se a porta estiver ocupada, use `APP_PORT=8090 docker compose up -d`.

Encerrar (`-v` também apaga o banco):

```bash
docker compose down -v
```

---

## Estrutura

```
.
├── docker-compose.yml     # app (PHP/Apache) + db (MySQL 8)
├── Dockerfile
├── database/
│   ├── schema.sql         # usuarios e lembretes
│   └── seed.php           # conta demo, senha gerada na hora
├── src/
│   ├── config.php · db.php · helpers.php
│   ├── auth.php           # sessão, login, cadastro, guarda de rota
│   └── LembreteRepository.php
├── views/
│   ├── layout.php · layout-fim.php
│   └── icones.php         # catálogo de ícones dos lembretes
└── public/                # raiz do servidor
    ├── index.php          # painel de lembretes
    ├── login.php · cadastro.php · logout.php
    ├── lembrete-form.php · lembrete-concluir.php · lembrete-excluir.php
    └── assets/
```

---

## Isolamento entre contas

Todo método do repositório recebe o id do dono e filtra por ele na própria
consulta:

```sql
DELETE FROM lembretes WHERE id = :id AND usuario_id = :usuario
```

Trocar o id na requisição não alcança o dado de outra pessoa: a consulta
simplesmente não encontra a linha. Verificado em teste, criando uma segunda
conta e tentando excluir e concluir lembretes alheios com token CSRF válido.

---

## Stack

PHP 8.3 · MySQL 8 · Apache · Docker · CSS puro

---

## Licença

MIT
