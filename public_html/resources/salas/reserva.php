<?php
session_start();
if (!isset($_SESSION['numero'])) {
    echo "Acesso negado. Faça login para reservar uma sala.";
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$utilizador_numero = $_SESSION['numero'];

// Obter lista de salas
$sql_salas = "SELECT id, nome FROM salas";
$resultado_salas = $conn->query($sql_salas);

if ($resultado_salas->num_rows > 0) {
    $salas = $resultado_salas->fetch_all(MYSQLI_ASSOC);
} else {
    $salas = []; // Caso não existam salas na base de dados
}

// Variáveis para exibir horários
$horarios_reservados = [];
$data_reserva = null;
$sala_id = null;

// Processar requisição GET para exibir horários reservados
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['sala_id'], $_GET['data_reserva'])) {
    $sala_id = (int)$_GET['sala_id'];
    $data_reserva = $_GET['data_reserva'];

    $sql_horarios = $conn->prepare("SELECT horario_inicio, horario_fim FROM reservas WHERE sala_id = ? AND data_reserva = ?");
    $sql_horarios->bind_param("is", $sala_id, $data_reserva);
    $sql_horarios->execute();
    $resultado_horarios = $sql_horarios->get_result();

    if ($resultado_horarios->num_rows > 0) {
        $horarios_reservados = $resultado_horarios->fetch_all(MYSQLI_ASSOC);
    }
}

// Processar requisição POST para realizar a reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sala_id = (int)$_POST['sala_id'];
    $data_reserva = $_POST['data_reserva'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];

    $data_atual = new DateTime();
    $data_reserva_datetime = new DateTime($data_reserva);
    $intervalo = $data_atual->diff($data_reserva_datetime);

    if ($intervalo->days < 2 || $data_reserva_datetime < $data_atual) {
        echo "Erro: Reservas só podem ser feitas com pelo menos 2 dias de antecedência.";
        exit;
    }

    // Verificar se o número do utilizador existe na tabela 'utilizadores'
    $sql_check_user = $conn->prepare("SELECT COUNT(*) FROM utilizadores WHERE numero = ?");
    $sql_check_user->bind_param("i", $utilizador_numero);
    $sql_check_user->execute();
    $resultado_check_user = $sql_check_user->get_result();
    $user_exists = $resultado_check_user->fetch_row()[0];

    if (!$user_exists) {
        echo "Erro: O utilizador não existe na base de dados.";
        exit;
    }

    // Verificar disponibilidade do horário
    $sql_verificar = $conn->prepare("SELECT * FROM reservas WHERE sala_id = ? AND data_reserva = ? AND (horario_inicio < ? AND horario_fim > ?)");
    $sql_verificar->bind_param("isss", $sala_id, $data_reserva, $horario_fim, $horario_inicio);
    $sql_verificar->execute();
    $resultado_verificar = $sql_verificar->get_result();

    if ($resultado_verificar->num_rows > 0) {
        echo "Erro: Já existe uma reserva para este horário.";
        exit;
    }

    // Inserir a reserva na tabela
    $sql_inserir = $conn->prepare("INSERT INTO reservas (sala_id, utilizador_numero, data_reserva, horario_inicio, horario_fim) VALUES (?, ?, ?, ?, ?)");
    $sql_inserir->bind_param("iisss", $sala_id, $utilizador_numero, $data_reserva, $horario_inicio, $horario_fim);

    if ($sql_inserir->execute()) {
        echo "Reserva realizada com sucesso!";
    } else {
        echo "Erro ao realizar a reserva: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Salas</title>
    <link rel="stylesheet" href="reserva.css">
</head>
<body>
    <div class="container">
        <h1>Reserva de Salas</h1>

        <!-- Formulário para selecionar a sala e a data -->
        <form method="GET" action="" class="form">
            <label for="sala_id">Escolha a Sala:</label>
            <select name="sala_id" required>
                <?php if (!empty($salas)): ?>
                    <?php foreach ($salas as $sala): ?>
                        <option value="<?php echo $sala['id']; ?>" <?php echo (isset($sala_id) && $sala_id == $sala['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sala['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">Nenhuma sala disponível</option>
                <?php endif; ?>
            </select>

            <label for="data_reserva">Escolha a Data:</label>
            <input type="date" name="data_reserva" value="<?php echo htmlspecialchars($data_reserva); ?>" required>

            <input type="submit" value="Ver Horários">
        </form>

        <!-- Exibir horários reservados -->
        <?php if (!empty($horarios_reservados)): ?>
            <h2>Horários Reservados</h2>
            <p>Data: <strong><?php echo htmlspecialchars($data_reserva); ?></strong></p>
            <ul class="reservas-lista">
                <?php foreach ($horarios_reservados as $horario): ?>
                    <li><?php echo $horario['horario_inicio'] . " - " . $horario['horario_fim']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($sala_id && $data_reserva): ?>
            <p>Não há horários reservados para esta data.</p>
        <?php endif; ?>

        <!-- Formulário para realizar uma reserva -->
        <?php if ($sala_id && $data_reserva): ?>
            <form method="POST" action="" class="form">
                <input type="hidden" name="sala_id" value="<?php echo $sala_id; ?>">
                <input type="hidden" name="data_reserva" value="<?php echo htmlspecialchars($data_reserva); ?>">

                <label for="horario_inicio">Horário de Início:</label>
                <input type="time" name="horario_inicio" required>

                <label for="horario_fim">Horário de Fim:</label>
                <input type="time" name="horario_fim" required>

                <input type="submit" value="Reservar">
            </form>
        <?php endif; ?>

        <a href="/index.php" class="voltar">Sair</a>
    </div>
</body>
</html>
