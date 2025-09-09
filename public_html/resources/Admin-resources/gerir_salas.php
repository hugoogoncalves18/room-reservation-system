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

// Buscar todas as salas
$sql_salas = "SELECT * FROM salas";
$result_salas = $conn->query($sql_salas);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Salas</title>
    <link rel="stylesheet" href="gerir_salas.css">
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

        .sala {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .sala h2 {
            margin-bottom: 10px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px 0;
            background: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerir Salas</h1>
        
        <p>Aqui você pode ver todas as salas e escolher entre editar ou visualizar reservas. Apenas administradores têm acesso a esta página.</p>

        <?php while ($sala = $result_salas->fetch_assoc()): ?>
            <div class="sala">
                <h2><?php echo htmlspecialchars($sala['nome']); ?></h2>
                <p><strong>Capacidade:</strong> <?php echo htmlspecialchars($sala['capacidade']); ?> pessoas</p>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($sala['descricao']); ?></p>

                <!-- Botões para Editar e Visualizar Reservas -->
                <a href="/resources/salas/editar_sala.php?sala_id=<?php echo $sala['id']; ?>" class="button">Editar Sala</a>
                <a href="ver_reserva.php?sala_id=<?php echo $sala['id']; ?>" class="button">Ver Reservas da Sala</a>
            </div>
        <?php endwhile; ?>

        <a href="/admin.php" class="button">Voltar ao Dashboard</a>
    </div>
</body>
</html>

<?php
// Fechar a conexão
$conn->close();
?>
