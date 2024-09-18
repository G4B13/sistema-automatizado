<?php
// Inclua o arquivo de conexão com o banco de dados
require 'db.php';

// Verifique se o token foi fornecido via GET
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Prepare uma consulta para verificar o token
    $stmt = $conn->prepare("SELECT id FROM users WHERE token = ? AND token_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    // Verifique se o token é válido
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId);
        $stmt->fetch();
        
        // Atualize o status do token para confirmado
        $stmt = $conn->prepare("UPDATE users SET token_verified = 1, token = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Conta confirmada com sucesso!');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "<script>alert('Erro ao confirmar a conta.'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Token inválido ou já utilizado.'); window.location.href = 'login.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    // Caso o token não esteja presente na URL
    echo "<script>alert('Token não fornecido.'); window.location.href = 'login.php';</script>";
}
?>

