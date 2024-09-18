<?php
session_start();
require 'conecta.php'; // Inclua o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redireciona para o login se não estiver autenticado
    exit();
}

// Obtém o ID e nome do usuário da sessão
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Busca os lembretes do usuário do banco de dados
$sql = "SELECT id, title, description, reminder_date, reminder_time FROM reminders WHERE user_id = ? ORDER BY reminder_date, reminder_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reminders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Função para formatar a data e hora
function formatDateTime($date, $time) {
    return date("d/m/Y H:i", strtotime($date . ' ' . $time));
}

// Obtém a data e hora atuais
$current_date_time = date("Y-m-d H:i");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema LUA - Tela Principal</title>
    <link href="https://fonts.googleapis.com/css2?family=Karla:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos conforme fornecido antes */
    </style>
    <script>
        // Verifica se o usuário não está autenticado e exibe uma mensagem
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Usuário não autenticado! Faça login para ter acesso ao sistema.');
                window.location.href = 'login.php';
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <div class="main-container">
        <img src="lua-logo.png" alt="Logo do Sistema LUA">
        <h1>Sistema LUA</h1>
        <div class="user-name">Bem-vindo, <?php echo htmlspecialchars($username); ?></div>
        <div class="sorting-options">
            <label for="sort-by">Ordenar por:</label>
            <select id="sort-by">
                <option value="name">Nome</option>
                <option value="date">Data</option>
            </select>
        </div>
        <div class="reminders">
            <h2>Seus Lembretes</h2>
            <?php
            if (count($reminders) > 0) {
                foreach ($reminders as $reminder) {
                    $reminder_datetime = formatDateTime($reminder['reminder_date'], $reminder['reminder_time']);
                    $is_upcoming = (strtotime($reminder_datetime) > strtotime($current_date_time) && strtotime($reminder_datetime) - strtotime($current_date_time) <= 86400); // 24 horas
                    echo '<div class="reminder-item" style="' . ($is_upcoming ? 'border: 2px solid var(--accent-color);' : '') . '">';
                    echo '<h3>' . htmlspecialchars($reminder['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars($reminder['description']) . '</p>';
                    echo '<p><strong>Data e Hora:</strong> ' . htmlspecialchars($reminder_datetime) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>Você não tem lembretes.</p>';
            }
            ?>
        </div>
        <div class="options">
            <button onclick="location.href='logout.php'">Sair</button>
            <button onclick="location.href='delete-account.php'">Excluir Conta</button>
        </div>
        <div class="footer">
            <p>Desenvolvido por Gabriel C.</p>
        </div>
    </div>
</body>
</html>
