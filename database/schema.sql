-- Schema do Sistema LUA.
-- Carregado automaticamente pelo container MySQL na primeira subida.
--
-- O schema anterior definia users(name, email, password_hash), mas o login
-- consultava as colunas `username` e `password`, que não existiam. Aqui os
-- nomes seguem um padrão único, em português, igual ao usado no código.

CREATE TABLE IF NOT EXISTS usuarios (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(120) NOT NULL,
    email     VARCHAR(160) NOT NULL,
    senha     VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_usuarios_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lembretes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    titulo     VARCHAR(160) NOT NULL,
    descricao  TEXT         NULL,
    quando     DATETIME     NOT NULL,
    -- Ícone escolhido pelo usuário. A rotina é reconhecida pelo desenho
    -- antes do texto, que é o ponto do projeto.
    icone      VARCHAR(24)  NOT NULL DEFAULT 'relogio',
    concluido  TINYINT(1)   NOT NULL DEFAULT 0,
    criado_em  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_lembretes_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,

    -- Cobre a consulta da tela principal: lembretes de um usuário,
    -- ordenados por conclusão e data.
    KEY idx_lembretes_usuario_quando (usuario_id, concluido, quando)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
