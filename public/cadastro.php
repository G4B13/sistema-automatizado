<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

sessao_iniciar();

if (auth_usuario() !== null) {
    redirecionar('index.php');
}

$erros = [];
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $nome  = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($nome === '') {
        $erros['nome'] = 'Escreva seu nome.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'E-mail inválido.';
    }

    if (strlen($senha) < 8) {
        $erros['senha'] = 'A senha precisa ter pelo menos 8 caracteres.';
    }

    if ($erros === []) {
        $falha = auth_registrar($nome, $email, $senha);

        if ($falha === null) {
            flash('Conta criada. Bem-vindo ao Sistema LUA.');
            redirecionar('index.php');
        }

        $erros['email'] = $falha;
    }
}

$titulo = 'Criar conta | Sistema LUA';
require __DIR__ . '/../views/layout.php';
?>

<div class="cartao cartao--estreito">
  <img class="logo-entrada" src="assets/logo.svg" alt="" width="52" height="52">
  <h1>Criar conta</h1>
  <p class="apoio">Leva menos de um minuto.</p>

  <form method="post" class="formulario">
    <?= csrf_campo() ?>

    <div class="campo">
      <label for="nome">Como você se chama?</label>
      <input id="nome" name="nome" type="text" required autofocus autocomplete="name"
        value="<?= e($nome) ?>">
      <?php if (isset($erros['nome'])): ?><small class="erro"><?= e($erros['nome']) ?></small><?php endif; ?>
    </div>

    <div class="campo">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required autocomplete="username" value="<?= e($email) ?>">
      <?php if (isset($erros['email'])): ?><small class="erro"><?= e($erros['email']) ?></small><?php endif; ?>
    </div>

    <div class="campo">
      <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" required autocomplete="new-password">
      <small class="apoio">Mínimo de 8 caracteres.</small>
      <?php if (isset($erros['senha'])): ?><small class="erro"><?= e($erros['senha']) ?></small><?php endif; ?>
    </div>

    <button class="btn btn--primario btn--grande btn--bloco" type="submit">Criar conta</button>
  </form>

  <p class="apoio rodape-cartao">
    Já tem conta? <a href="login.php">Entrar</a>
  </p>
</div>

<?php require __DIR__ . '/../views/layout-fim.php'; ?>
