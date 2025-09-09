<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = "";  // Variável para armazenar mensagens de erro

// Verificar as credenciais
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $pass = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']) ? true : false;

    // Consultar o banco de dados para encontrar o utilizador
    $sql = "SELECT * FROM utilizadores WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numero);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar a senha
        if (password_verify($pass, $user['password'])) {
            $_SESSION['numero'] = $numero;  // Inicia a sessão do utilizador
            $_SESSION['nivel'] = $user['nivel'];  // Armazena o nível de acesso na sessão

            // Se "Lembrar-me" foi marcado
            if ($rememberMe) {
                // Gerar um token único para "Lembrar-me"
                $token = bin2hex(random_bytes(32));
                $expiry = date("Y-m-d H:i:s", strtotime("+30 days"));
            
                // Salvar o token no banco de dados
                $sql = "INSERT INTO remember_tokens (utilizador_numero, token, created_at) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $user['numero'], $token, $expiry);
                $stmt->execute();
            
                // Armazenar o token no cookie
                setcookie("remember_token", $token, strtotime("+30 days"), "/", "", true, true);
                setcookie("utilizador_numero", $user['numero'], strtotime("+30 days"), "/", "", true, true);
            }
            
            // Redirecionar o utilizador para a página principal
            header("Location: /index.php");
            exit();
        } else {
            $errorMessage = "Algo está errado, tente novamente.";  // Mensagem de erro
        }
    } else {
        $errorMessage = "Algo está errado, tente novamente.";  // Mensagem de erro para login inválido
    }

    $stmt->close();
}

$conn->close();
?>

<!-- Exibir a mensagem de erro, se houver -->
<?php if ($errorMessage): ?>
    <div style="color: red; text-align: center;">
        <?php echo $errorMessage; ?>
    </div>
<?php endif; ?>