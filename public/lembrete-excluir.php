<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/LembreteRepository.php';

sessao_iniciar();
auth_exigir_login();

// Só POST: alterar estado por GET permitia disparo via link ou imagem.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

csrf_validar();

$id = (int) ($_POST['id'] ?? 0);
$usuario = auth_usuario();

if ($id <= 0) {
    flash('Lembrete inválido.', 'erro');
    redirecionar('index.php');
}

LembreteRepository::criar()->excluir($id, $usuario['id']);
flash('Lembrete excluído.');

redirecionar('index.php');
