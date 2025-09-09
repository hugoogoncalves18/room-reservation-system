<?php
session_start();

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saw";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    error_log("Erro na conexão: " . $conn->connect_error);
    die("Erro ao conectar ao banco de dados.");
}

// Classe para validação
class UtilizadorValidator {
    private $errors = [];

    public function validarNumero($numero) {
        if (empty($numero)) {
            $this->errors['numero'] = "Número é obrigatório.";
        } elseif (!is_numeric($numero) || $numero <= 0) {
            $this->errors['numero'] = "Número deve ser um valor positivo.";
        }
    }

    public function validarNome($nome) {
        if (empty($nome)) {
            $this->errors['nome'] = "Nome é obrigatório.";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nome)) {
            $this->errors['nome'] = "Nome deve conter apenas letras e espaços.";
        }
    }

    public function validarLogin($login) {
        if (empty($login)) {
            $this->errors['login'] = "Login é obrigatório.";
        } elseif (strlen($login) < 5 || strlen($login) > 20) {
            $this->errors['login'] = "Login deve ter entre 5 e 20 caracteres.";
        }
    }

    public function validarEmail($email) {
        if (empty($email)) {
            $this->errors['email'] = "Email é obrigatório.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Email inválido.";
        }
    }

    public function validarPassword($password) {
        if (empty($password)) {
            $this->errors['password'] = "Password é obrigatória.";
        } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/", $password)) {
            $this->errors['password'] = "Password deve ter entre 8 e 20 caracteres, com pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.";
        }
    }

    public function validarRepetirPassword($password, $repetirPassword) {
        if ($password !== $repetirPassword) {
            $this->errors['repetir_password'] = "Passwords não coincidem.";
        }
    }

    public function validarUtilizador($dados) {
        $this->validarNumero($dados['numero']);
        $this->validarNome($dados['nome']);
        $this->validarLogin($dados['login']);
        $this->validarEmail($dados['email']);
        $this->validarPassword($dados['password']);
        $this->validarRepetirPassword($dados['password'], $dados['repetir_password']);
        return empty($this->errors);
    }

    public function getErros() {
        return $this->errors;
    }
}

// Receber dados do formulário
$dadosUtilizador = [
    'numero' => $_POST['numero'] ?? null,
    'nome' => $_POST['nome'] ?? null,
    'login' => $_POST['login'] ?? null,
    'email' => $_POST['email'] ?? null,
    'password' => $_POST['password'] ?? null,
    'repetir_password' => $_POST['repetir_password'] ?? null,
];

$validador = new UtilizadorValidator();

if ($validador->validarUtilizador($dadosUtilizador)) {
    // Dados válidos
    $numero = $dadosUtilizador['numero'];
    $nome = $dadosUtilizador['nome'];
    $login = $dadosUtilizador['login'];
    $email = $dadosUtilizador['email'];
    $password = password_hash($dadosUtilizador['password'], PASSWORD_DEFAULT);
    $nivel = 0; // Nível padrão

    // Upload de imagem
    $target_dir = "uploads/imagem_perfil/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $profileImagePath = null;
    if (isset($_FILES['imagem_perfil']) && $_FILES['imagem_perfil']['error'] == 0) {
        $imageFileType = strtolower(pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes) && $_FILES['imagem_perfil']['size'] <= 2097152) { // Máx 2MB
            $newFileName = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $newFileName;

            if (move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $target_file)) {
                $profileImagePath = $target_file;
            } else {
                error_log("Erro ao carregar imagem.");
                die("Erro ao carregar imagem.");
            }
        } else {
            die("Formato ou tamanho de imagem inválido.");
        }
    }

    // Inserir no banco
    $stmt = $conn->prepare("INSERT INTO utilizadores (numero, nome, login, password, nivel, email, imagem_perfil) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiss", $numero, $nome, $login, $password, $nivel, $email, $profileImagePath);

    if ($stmt->execute()) {
        header("Location: /resources/users/loginForm/login.html");
        exit();
    } else {
        error_log("Erro ao inserir: " . $stmt->error);
        die("Erro ao inserir no banco de dados.");
    }

    $stmt->close();
} else {
    error_log("Erros na validação: " . json_encode($validador->getErros()));
    echo "Erros encontrados:<br>";
    foreach ($validador->getErros() as $campo => $erro) {
        echo htmlspecialchars("$campo: $erro") . "<br>";
    }
}

$conn->close();
?>
