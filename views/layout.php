<?php

declare(strict_types=1);

$usuario = auth_usuario();
$aviso = flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($titulo ?? 'Sistema LUA') ?></title>
  <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="assets/css/app.css">
</head>

<body>
  <?php if ($usuario !== null): ?>
    <header class="topo">
      <div class="wrap topo-inner">
        <a class="marca" href="index.php">
          <img src="assets/logo.svg" alt="" width="30" height="30">
          Sistema LUA
        </a>
        <div class="usuario">
          <span class="usuario-nome"><?= e($usuario['nome']) ?></span>
          <a class="btn btn--sutil" href="logout.php">Sair</a>
        </div>
      </div>
    </header>
  <?php endif; ?>

  <main class="wrap conteudo">
    <?php if ($aviso !== null): ?>
      <p class="aviso aviso--<?= e($aviso['tipo']) ?>" role="status"><?= e($aviso['mensagem']) ?></p>
    <?php endif; ?>
