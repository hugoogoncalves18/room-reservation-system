<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$numero = $_POST['numero'];

// Eliminar utilizador
$sql = "DELETE FROM utilizadores WHERE numero = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $numero);

if ($stmt->execute()) {
    echo "Utilizador eliminado com sucesso!";
} else {
    echo "Erro ao eliminar utilizador: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>