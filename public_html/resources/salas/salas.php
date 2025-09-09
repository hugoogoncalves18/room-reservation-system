<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta para buscar as salas
$sql = "SELECT nome, capacidade, descricao, imagem FROM salas "; // Exemplo de filtro
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas Disponíveis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1em 2em;
            text-align: center;
            font-size: 24px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 2em;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1em;
        }

        .card-body h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }

        .card-body p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .card-body button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .card-body button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>Salas Disponíveis</header>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($row['imagem']); ?>" alt="Imagem da Sala">
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
                        <p>Capacidade: <?php echo htmlspecialchars($row['capacidade']); ?> pessoas</p>
                        <p><?php echo htmlspecialchars($row['descricao']); ?></p>
                        <button>Reservar</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma sala disponível no momento.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
