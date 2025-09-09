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

// Atualizar os dados do usuário quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password']; // Atualiza a senha apenas se foi fornecida
    $imagem_perfil = $user['imagem_perfil']; // Manter a imagem de perfil antiga caso não tenha sido alterada

    // Se uma nova imagem foi enviada
    if (isset($_FILES['imagem_perfil']) && $_FILES['imagem_perfil']['error'] == 0) {
        $imagem_nome = $_FILES['imagem_perfil']['name'];
        $imagem_temp = $_FILES['imagem_perfil']['tmp_name'];
        $imagem_ext = strtolower(pathinfo($imagem_nome, PATHINFO_EXTENSION));
        
        // Validar se é uma imagem válida
        $extensoes_permitidas = ['jpg', 'jpeg', 'png'];
        if (in_array($imagem_ext, $extensoes_permitidas)) {
            $imagem_destino = "uploads/" . uniqid() . "." . $imagem_ext;
            move_uploaded_file($imagem_temp, $imagem_destino);
            $imagem_perfil = $imagem_destino; // Atualiza a imagem de perfil com o novo caminho
        } else {
            echo "Formato de imagem inválido.";
            exit;
        }
    }

    // Atualizar no banco de dados
    $sql = "UPDATE utilizadores SET email = ?, password = ?, imagem_perfil = ? WHERE login = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $email, $password, $imagem_perfil, $numero);
        
        if ($stmt->execute()) {
            echo "Dados atualizados com sucesso!";
            echo '<br><a href="perfil.php">Ver meu perfil</a>';
        } else {
            echo "Erro ao atualizar os dados: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conn->error;
    }

    $conn->close();
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
            color: #333;
        }

        form label {
            font-size: 14px;
            color: #555;
        }

        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            transition: color 0.3s ease;
            text-align: center;
        }

        .back-link:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Perfil</h1>
        <form action="editar_perfil.php" method="post" enctype="multipart/form-data">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Nova Senha:</label>
            <input type="password" id="password" name="password" placeholder="Digite uma nova senha (ou deixe em branco para manter a atual)">

            <label for="imagem_perfil">Imagem de Perfil:</label>
            <input type="file" id="imagem_perfil" name="imagem_perfil">

            <input type="submit" value="Salvar Alterações">
        </form>
        <a href="perfil.php" class="back-link">Voltar</a>
    </div>
</body>
</html>
