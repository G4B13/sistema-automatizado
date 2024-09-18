<?php
$servername = "localhost";
$username = "root"; // padrão do Laragon
$password = ""; // padrão do Laragon
$dbname = "sistema_lua";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
