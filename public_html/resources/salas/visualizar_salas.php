<?php
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consultar as salas
$sql = "SELECT * FROM salas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Salas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1em;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 2em auto;
            padding: 1em;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sala {
            border: 1px solid #ddd;
            margin-bottom: 1em;
            padding: 1em;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .sala img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 1em;
        }

        .sala h2 {
            margin-bottom: 0.5em;
        }

        .action-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 0.5em 1em;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .action-button:hover {
            background-color: #45a049;
        }

        .back-button {
            display: inline-block;
            margin-top: 1em;
            padding: 0.5em 1em;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>Salas Disponíveis</h1>
    </header>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="sala">
                    <img src="/resources/Admin-resources/uploads/<?php echo $row['imagem']; ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>">
                    <h2><?php echo htmlspecialchars($row['nome']); ?></h2>
                    <p><strong>Capacidade:</strong> <?php echo htmlspecialchars($row['capacidade']); ?> pessoas</p>
                    <p><?php echo htmlspecialchars($row['descricao']); ?></p>
                    
                    <?php if (isset($_SESSION['numero']) && isset($_SESSION['nivel'])): ?>
                        <?php if ($_SESSION['nivel'] == 0): ?>
                            <a href='reserva.php?sala_id=<?php echo $row['id']; ?>' class='action-button'>Reservar</a>
                        <?php elseif ($_SESSION['nivel'] == 1): ?>
                            <a href='/resources/Admin-resources/gerir_salas.php?sala_id=<?php echo $row['id']; ?>' class='action-button'>Gerir Sala</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p><strong>Para mais opções, faça login.</strong></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma sala disponível no momento.</p>
        <?php endif; ?>

        <a href="/index.php" class="back-button">Voltar ao Início</a>
    </div>
</body>
</html>
