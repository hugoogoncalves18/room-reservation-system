<?php
session_start();
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "saw"; 

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verificar se o usuário é administrador
if ($_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit;
}

// Verificar se o parâmetro sala_id foi enviado
if (!isset($_GET['sala_id'])) {
    die("Sala não especificada.");
}

$sala_id = intval($_GET['sala_id']);

// Buscar informações da sala
$sql = "SELECT * FROM salas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sala_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Sala não encontrada.");
}

$sala = $result->fetch_assoc();

// Atualizar informações da sala
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $capacidade = intval($_POST['capacidade']);
    $imagem = $_POST['imagem'];

    $sql_update = "UPDATE salas SET nome = ?, descricao = ?, capacidade = ?, imagem = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssisi", $nome, $descricao, $capacidade, $imagem, $sala_id);

    if ($stmt_update->execute()) {
        $mensagem = "Sala atualizada com sucesso!";
        // Atualizar informações para exibição
        $sala['nome'] = $nome;
        $sala['descricao'] = $descricao;
        $sala['capacidade'] = $capacidade;
        $sala['imagem'] = $imagem;
    } else {
        $mensagem = "Erro ao atualizar a sala: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sala</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input, textarea {
            margin-top: 5px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .button {
            margin-top: 20px;
            padding: 10px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background: #45a049;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            background: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            border-radius: 5px;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #4CAF50;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Sala: <?php echo htmlspecialchars($sala['nome']); ?></h1>

        <?php if (isset($mensagem)): ?>
            <div class="message"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="nome">Nome da Sala:</label>
            <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($sala['nome']); ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" rows="5" required><?php echo htmlspecialchars($sala['descricao']); ?></textarea>

            <label for="capacidade">Capacidade:</label>
            <input type="number" name="capacidade" id="capacidade" value="<?php echo htmlspecialchars($sala['capacidade']); ?>" required>

            <label for="imagem">Imagem (URL):</label>
            <input type="text" name="imagem" id="imagem" value="<?php echo htmlspecialchars($sala['imagem']); ?>">

            <button type="submit" class="button">Salvar Alterações</button>
        </form>

        <a href="/resources/Admin-resources/gerir_salas.php" class="back-link">Voltar à Gestão de Salas</a>
    </div>
</body>
</html>

<?php
// Fechar a conexão
$conn->close();
?>
