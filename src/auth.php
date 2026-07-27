<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function sessao_iniciar(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);

    session_start();
}

/**
 * Autentica pelo e-mail.
 *
 * A versão anterior consultava as colunas `username` e `password`, que não
 * existiam na tabela: o schema define `name`, `email` e `password_hash`.
 * O login falhava sempre, em qualquer credencial.
 */
function auth_login(string $email, string $senha): bool
{
    $stmt = db()->prepare(
        'SELECT id, nome, email, senha FROM usuarios WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        return false;
    }

    if (password_needs_rehash($usuario['senha'], PASSWORD_DEFAULT)) {
        db()->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id')
            ->execute([
                ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                ':id'    => $usuario['id'],
            ]);
    }

    session_regenerate_id(true);

    $_SESSION['usuario'] = [
        'id'    => (int) $usuario['id'],
        'nome'  => $usuario['nome'],
        'email' => $usuario['email'],
    ];

    return true;
}

/**
 * Cria a conta e já autentica. Devolve null em caso de sucesso ou a
 * mensagem de erro quando o e-mail já estiver cadastrado.
 */
function auth_registrar(string $nome, string $email, string $senha): ?string
{
    $existe = db()->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
    $existe->execute([':email' => $email]);

    if ($existe->fetchColumn()) {
        return 'Já existe uma conta com este e-mail.';
    }

    db()->prepare(
        'INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)'
    )->execute([
        ':nome'  => $nome,
        ':email' => $email,
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
    ]);

    auth_login($email, $senha);

    return null;
}

function auth_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
}

function auth_usuario(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function auth_exigir_login(): void
{
    if (auth_usuario() === null) {
        redirecionar('login.php');
    }
}
