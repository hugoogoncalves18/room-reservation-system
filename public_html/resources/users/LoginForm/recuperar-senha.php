<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../../vendor/autoload.php';

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$errorMessage = ""; // Variável para armazenar mensagens de erro

// Verificar se a requisição foi feita via POST (envio do formulário)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Validação do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Por favor, insira um e-mail válido.";
    } else {
        // Verificar se o e-mail existe no banco de dados
        $sql = "SELECT * FROM utilizadores WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se o e-mail for encontrado no banco de dados
        if ($result->num_rows > 0) {
            // Obter os dados do utilizador
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32)); // Gerar um token único
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Definir a expiração do token (1 hora a partir de agora)

            // Inserir o token e sua expiração na tabela password_resets
            $sql = "INSERT INTO password_resets (utilizador_numero, token, expiry) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user['numero'], $token, $expiry);

            if ($stmt->execute()) {
                // Criar o link de recuperação
                $resetLink = "http://saw.pt/resources/users/LoginForm/mudarpass.php?token=$token";  // Link para a página mudarpass.php

                // Enviar o e-mail de recuperação
                $mail = new PHPMailer(true);
                try {
                    // Configuração SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'hwm01.g@gmail.com'; // E-mail de envio
                    $mail->Password = 'lccc oziq tewa lwwz'; // Senha de aplicativo do Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Configuração do e-mail
                    $mail->setFrom('sawcrsi@gmail.com', 'Recuperação de Senha');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperação de Senha';
                    $mail->Body = "Clique no link para recuperar sua senha: <a href='$resetLink'>$resetLink</a>";

                    // Enviar o e-mail
                    $mail->send();
                    echo "Se o e-mail estiver registado, receberá instruções para recuperar a senha.";
                } catch (Exception $e) {
                    $errorMessage = "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
                }
            } else {
                $errorMessage = "Erro ao salvar o token de recuperação. Tente novamente.";
            }
        } else {
            // Mensagem genérica para evitar exploração
            echo "Se o e-mail estiver registado, receberá instruções para recuperar a senha.";
        }

        // Fechar o statement
        $stmt->close();
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
