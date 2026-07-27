<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Acesso aos lembretes.
 *
 * Todo método recebe o id do dono e filtra por ele na própria consulta.
 * Assim um usuário nunca alcança o lembrete de outro, mesmo trocando o id
 * na requisição.
 */
final class LembreteRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public static function criar(): self
    {
        return new self(db());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listar(int $usuarioId, bool $somentePendentes = false): array
    {
        $sql = 'SELECT id, titulo, descricao, quando, concluido, icone
                FROM lembretes WHERE usuario_id = :usuario';

        if ($somentePendentes) {
            $sql .= ' AND concluido = 0';
        }

        $stmt = $this->pdo->prepare($sql . ' ORDER BY concluido, quando');
        $stmt->execute([':usuario' => $usuarioId]);

        return $stmt->fetchAll();
    }

    public function buscar(int $id, int $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, titulo, descricao, quando, concluido, icone
             FROM lembretes WHERE id = :id AND usuario_id = :usuario'
        );
        $stmt->execute([':id' => $id, ':usuario' => $usuarioId]);

        return $stmt->fetch() ?: null;
    }

    /**
     * @param array<string, string> $dados
     */
    public function inserir(int $usuarioId, array $dados): int
    {
        $this->pdo->prepare(
            'INSERT INTO lembretes (usuario_id, titulo, descricao, quando, icone)
             VALUES (:usuario, :titulo, :descricao, :quando, :icone)'
        )->execute([
            ':usuario'   => $usuarioId,
            ':titulo'    => $dados['titulo'],
            ':descricao' => $dados['descricao'],
            ':quando'    => $dados['quando'],
            ':icone'     => $dados['icone'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<string, string> $dados
     */
    public function atualizar(int $id, int $usuarioId, array $dados): void
    {
        $this->pdo->prepare(
            'UPDATE lembretes
             SET titulo = :titulo, descricao = :descricao, quando = :quando, icone = :icone
             WHERE id = :id AND usuario_id = :usuario'
        )->execute([
            ':id'        => $id,
            ':usuario'   => $usuarioId,
            ':titulo'    => $dados['titulo'],
            ':descricao' => $dados['descricao'],
            ':quando'    => $dados['quando'],
            ':icone'     => $dados['icone'],
        ]);
    }

    public function alternarConclusao(int $id, int $usuarioId): void
    {
        $this->pdo->prepare(
            'UPDATE lembretes SET concluido = NOT concluido
             WHERE id = :id AND usuario_id = :usuario'
        )->execute([':id' => $id, ':usuario' => $usuarioId]);
    }

    public function excluir(int $id, int $usuarioId): void
    {
        $this->pdo->prepare(
            'DELETE FROM lembretes WHERE id = :id AND usuario_id = :usuario'
        )->execute([':id' => $id, ':usuario' => $usuarioId]);
    }

    /**
     * @return array{total: int, pendentes: int, hoje: int}
     */
    public function resumo(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                COUNT(*) AS total,
                SUM(concluido = 0) AS pendentes,
                SUM(DATE(quando) = CURDATE()) AS hoje
             FROM lembretes WHERE usuario_id = :usuario'
        );
        $stmt->execute([':usuario' => $usuarioId]);
        $linha = $stmt->fetch();

        return [
            'total'     => (int) ($linha['total'] ?? 0),
            'pendentes' => (int) ($linha['pendentes'] ?? 0),
            'hoje'      => (int) ($linha['hoje'] ?? 0),
        ];
    }
}
