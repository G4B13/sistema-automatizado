<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

sessao_iniciar();

if (auth_usuario() !== null) {
    redirecionar('index.php');
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } elseif (auth_login($email, $senha)) {
        redirecionar('index.php');
    } else {
        // Mensagem única para os dois casos, sem revelar se o e-mail existe.
        $erro = 'E-mail ou senha incorretos.';
    }
}

$titulo = 'Entrar | Sistema LUA';
require __DIR__ . '/../views/layout.php';
?>

<div class="cartao cartao--estreito">
  <img class="logo-entrada" src="assets/logo.svg" alt="" width="52" height="52">
  <h1>Sistema LUA</h1>
  <p class="apoio">Lembretes visuais para a rotina do dia a dia.</p>

  <?php if ($erro !== null): ?>
    <p class="aviso aviso--erro" role="alert"><?= e($erro) ?></p>
  <?php endif; ?>

  <form method="post" class="formulario">
    <?= csrf_campo() ?>

    <div class="campo">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required autocomplete="username"
        value="<?= e($_POST['email'] ?? '') ?>">
    </div>

    <div class="campo">
      <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" required autocomplete="current-password">
    </div>

    <button class="btn btn--primario btn--grande btn--bloco" type="submit">Entrar</button>
  </form>

  <p class="apoio rodape-cartao">
    Ainda não tem conta? <a href="cadastro.php">Criar uma agora</a>
  </p>
</div>

<?php require __DIR__ . '/../views/layout-fim.php'; ?>
