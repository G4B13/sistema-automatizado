<?php
session_start();
require 'conecta.php'; // Inclua o arquivo de conexão com o banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém as credenciais do formulário e faz a sanitização
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Verifica se os campos estão vazios
    if (empty($username) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Prepara a consulta para evitar SQL Injection
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se o usuário existe
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifica a senha
            if (password_verify($password, $user['password'])) {
                // Cria a sessão do usuário
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redireciona para a página principal
                header("Location: main.php");
                exit();
            } else {
                $error = "Usuário ou senha inválidos.";
            }
        } else {
            $error = "Usuário ou senha inválidos.";
        }

        $stmt->close();
    }

    $conn->close();
} else {
    $error = "Método de requisição inválido.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema LUA - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Karla:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-black0: #000000;
            --color-black1: #121212;
            --color-black2: #212020;
            --color-white: #FFFFFF;
            --color-gray: #c4c2c2;
            --color-blue: #4c61ae;
            --color-blue2: #093bb1;
            --color-gold: #d4af37;
            --color-gold-light: #f4c542;
            --text-color: var(--color-gray);
            --border-color: var(--color-gray);
            --primary-color: var(--color-black1);
            --secondary-color: var(--color-blue);
            --accent-color: var(--color-gold);
            --accent-light: var(--color-gold-light);
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-size: 20px;
            display: grid;
            place-items: center;
            min-height: 100vh;
            font-family: 'Karla', sans-serif;
            overflow: hidden;
        }

        .login-container {
            position: relative;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
            text-align: center;
            z-index: 1;
            overflow: hidden;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .login-container img {
            width: 120px;
            margin-bottom: 20px;
        }

        .login-container h1 {
            color: var(--accent-color);
            font-size: 26px;
            margin: 0 0 20px 0;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .login-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 700;
            color: var(--accent-color);
            text-align: left;
        }

        .login-container input[type="text"], 
        .login-container input[type="password"] {
            width: 95%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid var(--accent-color);
            border-radius: 6px;
            background: var(--color-black2);
            color: var(--accent-color);
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .login-container input[type="text"]:focus, 
        .login-container input[type="password"]:focus {
            border-color: var(--accent-light);
            outline: none;
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
            color: var(--color-white);
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container input[type="submit"]:hover {
            background: linear-gradient(135deg, var(--accent-light), var(--accent-color));
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.5);
        }

        .login-container .footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-container .footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .login-container .footer a:hover {
            color: var(--accent-light);
        }

        .login-container .footer .create-account {
            display: block;
            margin-top: 10px;
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="lua-logo.png" alt="Logo do Sistema LUA">
        <h1>Sistema LUA</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="login_process.php" method="post">
            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Entrar">
        </form>
        <div class="footer">
            <a href="#">Esqueceu sua senha?</a>
            <a class="create-account" href="create.php">Criar Conta</a>
        </div>
    </div>
</body>
</html>
