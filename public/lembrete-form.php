<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/LembreteRepository.php';
require_once __DIR__ . '/../views/icones.php';

sessao_iniciar();
auth_exigir_login();

$usuario = auth_usuario();
$repositorio = LembreteRepository::criar();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editando = $id > 0;
$erros = [];

$lembrete = [
    'titulo' => '', 'descricao' => '', 'quando' => '', 'icone' => 'relogio',
];

if ($editando) {
    $registro = $repositorio->buscar($id, $usuario['id']);

    if ($registro === null) {
        flash('Lembrete não encontrado.', 'erro');
        redirecionar('index.php');
    }

    $lembrete = [
        'titulo'    => (string) $registro['titulo'],
        'descricao' => (string) $registro['descricao'],
        // O input datetime-local espera o formato Y-m-d\TH:i.
        'quando'    => (new DateTimeImmutable((string) $registro['quando']))->format('Y-m-d\TH:i'),
        'icone'     => (string) $registro['icone'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    foreach (array_keys($lembrete) as $campo) {
        $lembrete[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }

    if ($lembrete['titulo'] === '') {
        $erros['titulo'] = 'Escreva o que precisa ser lembrado.';
    }

    $quando = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $lembrete['quando']);

    if ($quando === false) {
        $erros['quando'] = 'Escolha a data e a hora.';
    }

    if (!isset(ICONES[$lembrete['icone']])) {
        // Nunca confia no valor do formulário: cai no padrão se vier
        // um ícone que não existe.
        $lembrete['icone'] = 'relogio';
    }

    if ($erros === []) {
        $dados = [
            'titulo'    => $lembrete['titulo'],
            'descricao' => $lembrete['descricao'],
            'quando'    => $quando->format('Y-m-d H:i:s'),
            'icone'     => $lembrete['icone'],
        ];

        if ($editando) {
            $repositorio->atualizar($id, $usuario['id'], $dados);
            flash('Lembrete atualizado.');
        } else {
            $repositorio->inserir($usuario['id'], $dados);
            flash('Lembrete criado.');
        }

        redirecionar('index.php');
    }
}

$titulo = ($editando ? 'Editar' : 'Novo') . ' lembrete | Sistema LUA';
require __DIR__ . '/../views/layout.php';
?>

<div class="cartao">
  <h1><?= $editando ? 'Editar lembrete' : 'Novo lembrete' ?></h1>

  <form method="post" class="formulario">
    <?= csrf_campo() ?>

    <div class="campo">
      <label for="titulo">O que precisa lembrar?</label>
      <input id="titulo" name="titulo" type="text" required autofocus
        placeholder="Tomar o remédio da manhã" value="<?= e($lembrete['titulo']) ?>">
      <?php if (isset($erros['titulo'])): ?><small class="erro"><?= e($erros['titulo']) ?></small><?php endif; ?>
    </div>

    <div class="campo">
      <label for="quando">Quando?</label>
      <input id="quando" name="quando" type="datetime-local" required value="<?= e($lembrete['quando']) ?>">
      <?php if (isset($erros['quando'])): ?><small class="erro"><?= e($erros['quando']) ?></small><?php endif; ?>
    </div>

    <div class="campo">
      <label for="descricao">Alguma observação? (opcional)</label>
      <textarea id="descricao" name="descricao" rows="3"
        placeholder="Depois do café, com água."><?= e($lembrete['descricao']) ?></textarea>
    </div>

    <fieldset class="campo">
      <legend>Escolha um desenho</legend>
      <div class="grade-icones">
        <?php foreach (ICONES as $chave => $dados): ?>
          <label class="opcao-icone">
            <input type="radio" name="icone" value="<?= e($chave) ?>"
              <?= $lembrete['icone'] === $chave ? 'checked' : '' ?>>
            <span class="opcao-icone-visual">
              <?= icone_svg($chave, 30) ?>
              <span><?= e($dados['rotulo']) ?></span>
            </span>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <div class="acoes">
      <button class="btn btn--primario btn--grande" type="submit">
        <?= $editando ? 'Salvar' : 'Criar lembrete' ?>
      </button>
      <a class="btn btn--sutil btn--grande" href="index.php">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../views/layout-fim.php'; ?>
