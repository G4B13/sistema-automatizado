<?php
// Inclua o arquivo de configuração do banco de dados
require 'conecta.php';

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém e valida os dados do formulário
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Verifica se os campos estão preenchidos
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Todos os campos são obrigatórios.'); window.location.href = 'register.html';</script>";
        exit();
    }

    // Verifica se o email já está em uso
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Este email já está em uso.'); window.location.href = 'register.html';</script>";
        exit();
    }

    // Criptografa a senha
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insere o novo usuário no banco de dados
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registro realizado com sucesso!'); 
                window.location.href = 'login.php'; 
              </script>";
    } else {
        echo "<script>alert('Erro ao registrar: " . $stmt->error . "'); window.location.href = 'register.html';</script>";
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
} else {
    // Se o formulário não foi enviado, redireciona para a página de registro
    header("Location: register.html");
    exit();
}
?>
