<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$errorMessage = ""; // Variável para armazenar mensagens de erro

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar se o token existe na tabela password_resets
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token válido, permitir alteração de senha
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if (empty($newPassword) || empty($confirmPassword)) {
                $errorMessage = "Por favor, preencha ambos os campos de senha.";
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = "As senhas não coincidem.";
            } else {
                // Atualizar a senha do usuário
                $user = $result->fetch_assoc();
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Atualizar a senha do usuário na tabela 'utilizadores'
                $updateSql = "UPDATE utilizadores SET password = ? WHERE numero = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("si", $hashedPassword, $user['utilizador_numero']);

                if ($updateStmt->execute()) {
                    // Deletar o token de recuperação após a senha ser alterada
                    $deleteSql = "DELETE FROM password_resets WHERE token = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("s", $token);
                    $deleteStmt->execute();

                    // Redirecionar para a página de login após a alteração de senha
                    header("Location: http://www.saw.pt/resources/users/loginForm/login.html");
                    exit;  // Certifique-se de chamar exit após o redirecionamento
                } else {
                    $errorMessage = "Erro ao atualizar a senha. Tente novamente.";
                }
            }
        }

    } else {
        $errorMessage = "Token inválido ou expirado.";
    }
} else {
    $errorMessage = "Token não encontrado.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mudar Senha</title>
    <link rel="stylesheet" href="mudarpass.css">
</head>
<body>

<div class="login-container">
    <h2>Mudar Senha</h2>

    <?php if ($errorMessage) { echo "<p class='error'>$errorMessage</p>"; } ?>

    <form action="mudarpass.php?token=<?php echo $token; ?>" method="POST">
        <label for="password">Nova Senha</label>
        <input type="password" id="password" name="password" placeholder="Digite a nova senha" required>

        <label for="confirm_password">Confirmar Senha</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirme sua nova senha" required>

        <button type="submit">Alterar Senha</button>
    </form>

    <div class="extra-buttons">
        <a href="/index.php">Voltar ao Início</a>
    </div>
</div>

</body>
</html>
