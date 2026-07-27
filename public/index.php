<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/LembreteRepository.php';
require_once __DIR__ . '/../views/icones.php';

sessao_iniciar();
auth_exigir_login();

$usuario = auth_usuario();
$repositorio = LembreteRepository::criar();

$somentePendentes = ($_GET['filtro'] ?? '') === 'pendentes';
$lembretes = $repositorio->listar($usuario['id'], $somentePendentes);
$resumo = $repositorio->resumo($usuario['id']);

$titulo = 'Meus lembretes | Sistema LUA';
require __DIR__ . '/../views/layout.php';
?>

<div class="cabecalho-pagina">
  <div>
    <h1>Olá, <?= e($usuario['nome']) ?></h1>
    <p class="apoio">
      <?= $resumo['pendentes'] ?> lembrete(s) por fazer, <?= $resumo['hoje'] ?> para hoje.
    </p>
  </div>
  <a class="btn btn--primario btn--grande" href="lembrete-form.php">
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4"
      stroke-linecap="round" aria-hidden="true">
      <path d="M12 5v14M5 12h14" />
    </svg>
    Novo lembrete
  </a>
</div>

<nav class="filtros" aria-label="Filtrar lembretes">
  <a class="filtro<?= $somentePendentes ? '' : ' filtro--ativo' ?>" href="index.php">
    Todos (<?= $resumo['total'] ?>)
  </a>
  <a class="filtro<?= $somentePendentes ? ' filtro--ativo' : '' ?>" href="index.php?filtro=pendentes">
    Por fazer (<?= $resumo['pendentes'] ?>)
  </a>
</nav>

<?php if ($lembretes === []): ?>
  <div class="vazio">
    <p><?= $somentePendentes ? 'Tudo em dia por aqui.' : 'Nenhum lembrete cadastrado ainda.' ?></p>
    <a class="btn btn--primario" href="lembrete-form.php">Criar o primeiro</a>
  </div>
<?php else: ?>
  <ul class="lista-lembretes">
    <?php foreach ($lembretes as $lembrete): ?>
      <?php
        $concluido = (bool) $lembrete['concluido'];
        $quando = new DateTimeImmutable((string) $lembrete['quando']);
        $atrasado = !$concluido && $quando < new DateTimeImmutable();
      ?>
      <li class="lembrete<?= $concluido ? ' lembrete--feito' : '' ?>">
        <span class="lembrete-icone" aria-hidden="true">
          <?= icone_svg((string) $lembrete['icone'], 30) ?>
        </span>

        <div class="lembrete-texto">
          <h2><?= e($lembrete['titulo']) ?></h2>
          <?php if (!empty($lembrete['descricao'])): ?>
            <p class="apoio"><?= e($lembrete['descricao']) ?></p>
          <?php endif; ?>
          <p class="lembrete-quando<?= $atrasado ? ' esta-atrasado' : '' ?>">
            <time datetime="<?= e($quando->format('c')) ?>">
              <?= e($quando->format('d/m/Y')) ?> às <?= e($quando->format('H:i')) ?>
            </time>
            <?= $atrasado ? ' (atrasado)' : '' ?>
          </p>
        </div>

        <div class="lembrete-acoes">
          <?php /* Alterna a conclusão. POST para não mudar estado por GET. */ ?>
          <form method="post" action="lembrete-concluir.php">
            <?= csrf_campo() ?>
            <input type="hidden" name="id" value="<?= (int) $lembrete['id'] ?>">
            <button class="btn-icone<?= $concluido ? ' btn-icone--feito' : '' ?>" type="submit"
              aria-label="<?= $concluido ? 'Marcar como não feito' : 'Marcar como feito' ?>">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"
                stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 6L9 17l-5-5" />
              </svg>
            </button>
          </form>

          <a class="btn-icone" href="lembrete-form.php?id=<?= (int) $lembrete['id'] ?>"
            aria-label="Editar <?= e($lembrete['titulo']) ?>">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" />
            </svg>
          </a>

          <form method="post" action="lembrete-excluir.php"
            onsubmit="return confirm('Excluir <?= e($lembrete['titulo']) ?>?')">
            <?= csrf_campo() ?>
            <input type="hidden" name="id" value="<?= (int) $lembrete['id'] ?>">
            <button class="btn-icone btn-icone--perigo" type="submit"
              aria-label="Excluir <?= e($lembrete['titulo']) ?>">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" />
              </svg>
            </button>
          </form>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php require __DIR__ . '/../views/layout-fim.php'; ?>
