<?php

declare(strict_types=1);

/**
 * Cria uma conta de demonstração com alguns lembretes.
 *
 * Uso: docker compose exec app php database/seed.php
 *
 * A senha vem de DEMO_SENHA ou é sorteada e impressa. Nenhum hash fica
 * versionado no repositório.
 */

if (PHP_SAPI !== 'cli') {
    exit('Este script só roda pela linha de comando.');
}

require __DIR__ . '/../src/db.php';

$email = getenv('DEMO_EMAIL') ?: 'demo@sistemalua.local';
$senha = getenv('DEMO_SENHA') ?: bin2hex(random_bytes(6));

$pdo = db();

$existe = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
$existe->execute([':email' => $email]);

if ($existe->fetchColumn()) {
    fwrite(STDERR, "Já existe uma conta com o e-mail {$email}. Nada a fazer.\n");
    exit(1);
}

$pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)')
    ->execute([
        ':nome'  => 'Conta de demonstração',
        ':email' => $email,
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
    ]);

$usuarioId = (int) $pdo->lastInsertId();

$exemplos = [
    ['Tomar o remédio da manhã', 'Depois do café, com água.', '+0 day 08:00', 'remedio'],
    ['Escovar os dentes',        'Antes de sair de casa.',    '+0 day 09:00', 'higiene'],
    ['Almoço',                   'Aquecer o prato por 2 min.', '+0 day 12:00', 'refeicao'],
    ['Beber água',               'Encher a garrafa de novo.',  '+0 day 15:00', 'agua'],
    ['Preparar a mochila',       'Conferir a lista de amanhã.', '+1 day 19:00', 'rotina'],
];

$inserir = $pdo->prepare(
    'INSERT INTO lembretes (usuario_id, titulo, descricao, quando, icone)
     VALUES (:usuario, :titulo, :descricao, :quando, :icone)'
);

foreach ($exemplos as [$titulo, $descricao, $quando, $icone]) {
    $inserir->execute([
        ':usuario'   => $usuarioId,
        ':titulo'    => $titulo,
        ':descricao' => $descricao,
        ':quando'    => date('Y-m-d H:i:s', strtotime($quando)),
        ':icone'     => $icone,
    ]);
}

echo "Conta de demonstração criada com " . count($exemplos) . " lembretes.\n";
echo "  E-mail: {$email}\n";
echo "  Senha:  {$senha}\n";
