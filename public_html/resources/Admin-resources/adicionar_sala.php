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

// Consultar dados
$sql = "SELECT numero, nome, login, nivel, email FROM utilizadores";
$result = $conn->query($sql);



if (isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $capacidade = $_POST['capacidade'];
    $descricao = $_POST['descricao'];
    $imagem = null;

    // Processo de upload de imagem
    if (!empty($_FILES['imagem']['name'])) {
        $target_dir = "uploads/"; // Caminho relativo
        // ou
        // $target_dir = $_SE
        // Verifica se o diretório existe, se não, cria
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["imagem"]["tmp_name"]);

        if ($check !== false) {
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                $imagem = htmlspecialchars(basename($_FILES["imagem"]["name"]));
            } else {
                echo "Erro ao enviar a imagem.";
                exit();
            }
        } else {
            echo "O arquivo enviado não é uma imagem.";
            exit();
        }
    }

    // Inserir os dados no banco
    $stmt = $conn->prepare("INSERT INTO salas (nome, capacidade, descricao, imagem) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $nome, $capacidade, $descricao, $imagem);

    if ($stmt->execute()) {
        echo "Sala adicionada com sucesso!";
    } else {
        echo "Erro ao adicionar a sala: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
