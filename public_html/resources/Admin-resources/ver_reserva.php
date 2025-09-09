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
$sql_sala = "SELECT nome FROM salas WHERE id = ?";
$stmt_sala = $conn->prepare($sql_sala);
$stmt_sala->bind_param("i", $sala_id);
$stmt_sala->execute();
$result_sala = $stmt_sala->get_result();

if ($result_sala->num_rows === 0) {
    die("Sala não encontrada.");
}

$sala = $result_sala->fetch_assoc();
$reservas = [];
$mensagem = "";

// Processar a seleção de data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_selecionada = $_POST['data_reserva'];

    // Buscar reservas para a data selecionada
    $sql_reservas = "SELECT horario_inicio, horario_fim, (SELECT nome FROM utilizadores WHERE numero = r.utilizador_numero) AS utilizador 
                     FROM reservas r 
                     WHERE sala_id = ? AND data_reserva = ?";
    $stmt_reservas = $conn->prepare($sql_reservas);
    $stmt_reservas->bind_param("is", $sala_id, $data_selecionada);
    $stmt_reservas->execute();
    $result_reservas = $stmt_reservas->get_result();

    if ($result_reservas->num_rows > 0) {
        while ($reserva = $result_reservas->fetch_assoc()) {
            $reservas[] = $reserva;
        }
    } else {
        $mensagem = "Sem reservas para o dia selecionado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas da Sala</title>
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
            margin-top: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="date"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .button {
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

        .reservas {
            margin-top: 20px;
        }

        .mensagem {
            margin-top: 20px;
            padding: 10px;
            background: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #f4f4f4;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reservas da Sala: <?php echo htmlspecialchars($sala['nome']); ?></h1>

        <form method="POST">
            <label for="data_reserva">Selecione a data:</label>
            <input type="date" name="data_reserva" id="data_reserva" required>
            <button type="submit" class="button">Ver Reservas</button>
        </form>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <?php if (!empty($reservas)): ?>
            <div class="reservas">
                <table>
                    <thead>
                        <tr>
                            <th>Horário de Início</th>
                            <th>Horário de Fim</th>
                            <th>Utilizador</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva['horario_inicio']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['horario_fim']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['utilizador']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a href="gerir_salas.php" class="back-link">Voltar à Gestão de Salas</a>
    </div>
</body>
</html>

<?php
// Fechar a conexão
$conn->close();
?>
