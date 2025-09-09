<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_COOKIE['remember_token']) && isset($_COOKIE['utilizador_numero'])) {
    $token = $_COOKIE['remember_token'];
    $userId = $_COOKIE['utilizador_numero'];

    $sql = "SELECT * FROM remember_tokens WHERE utilizador_numero = ? AND token = ? AND created_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['id'] = $userId;
        echo "Bem-vindo de volta!";
    } else {
        echo "Token expirado ou inválido.";
        setcookie("remember_token", "", time() - 3600, "/");
        setcookie("utilizador_numero", "", time() - 3600, "/");
    }
    $stmt->close();
}
$conn->close();
?>
