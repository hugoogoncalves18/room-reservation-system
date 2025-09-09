<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado (se a sessão foi iniciada corretamente)
if (!isset($_SESSION['numero'])) {
    // Se não estiver autenticado, redireciona para a página de login
    header("Location: login.html");
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Buscar os dados do usuário logado com base no 'numero' da sessão
$numero = $_SESSION['numero']; // O número ou login armazenado na sessão

$sql = "SELECT * FROM utilizadores WHERE numero = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $numero);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se o usuário for encontrado, pegar os dados
    $user = $result->fetch_assoc();
} else {
    // Se não encontrar o usuário no banco de dados
    echo "Usuário não encontrado!";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .profile-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .profile-container h2 {
            margin-bottom: 20px;
        }
        .profile-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-container p {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .profile-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .profile-container a:hover {
            background-color: #45a049;
        }

        .edit-button {
            background-color: #007BFF;
            margin-left: 10px;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>Meu Perfil</h2>
    
    <!-- Exibição da imagem de perfil -->
    <img src="<?php echo htmlspecialchars($user['imagem_perfil'] ?? 'default-profile.png'); ?>" alt="Foto de Perfil">
    
    <p><strong>Nome:</strong> <?php echo htmlspecialchars($user['nome']); ?></p>
    <p><strong>Login:</strong> <?php echo htmlspecialchars($user['login']); ?></p>
    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
   
    
    <a href="/index.php">Voltar ao inicio!</a>
    <a href="/resources/users/LoginForm/editar_perfil.php" class="edit-button">Editar Perfil</a>
</div>

</body>
</html>
