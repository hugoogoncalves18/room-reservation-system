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

// Verificar se um número foi passado via GET para editar
if (isset($_GET['numero']) && !empty($_GET['numero'])) {
    $numero = filter_var($_GET['numero'], FILTER_SANITIZE_NUMBER_INT);

    // Buscar dados do utilizador
    $sql = "SELECT numero, nome, login, nivel, email FROM utilizadores WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $numero);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o utilizador existe
    if ($result->num_rows > 0) {
        $utilizador = $result->fetch_assoc();
    } else {
        echo "Utilizador não encontrado.";
        exit;
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar os dados do utilizador
    $numero = filter_var($_POST['numero'], FILTER_SANITIZE_NUMBER_INT);
    $nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
    $login = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
    $nivel = filter_var($_POST['nivel'], FILTER_SANITIZE_NUMBER_INT);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Verificar se o e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido!";
        exit;
    }

    $sql = "UPDATE utilizadores SET nome = ?, login = ?, nivel = ?, email = ? WHERE numero = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssisi", $nome, $login, $nivel, $email, $numero);

        if ($stmt->execute()) {
            echo "Dados atualizados com sucesso!";
            echo '<br><a href="consultar.php">Voltar</a>';
        } else {
            echo "Erro ao atualizar os dados: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conn->error;
    }
    $conn->close();
    exit;
} else {
    echo "Requisição inválida.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Utilizador</title>
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

        form input {
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
        <h1>Editar Utilizador</h1>
        <form action="editar.php" method="post">
            <input type="hidden" name="numero" value="<?php echo htmlspecialchars($utilizador['numero']); ?>">
            
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($utilizador['nome']); ?>" required>

            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($utilizador['login']); ?>" required>

            <label for="nivel">Nível:</label>
            <input type="number" id="nivel" name="nivel" value="<?php echo htmlspecialchars($utilizador['nivel']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($utilizador['email']); ?>" required>

            <input type="submit" value="Salvar Alterações">
        </form>
        <a href="consultar.php" class="back-link">Voltar</a>
    </div>
</body>
</html>
