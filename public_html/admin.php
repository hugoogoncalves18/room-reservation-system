<?php
// Iniciar a sessão de forma segura
session_start([
    'cookie_lifetime' => 3600, // Define o tempo de vida do cookie da sessão
    'cookie_secure' => true,  // Garante que o cookie só será enviado via HTTPS
    'cookie_httponly' => true, // Impede o acesso ao cookie via JavaScript
    'use_strict_mode' => true, // Garante que apenas IDs de sessão válidos são aceitos
]);

// Validação adicional para evitar manipulação de sessão
if (!isset($_SESSION['numero']) || !isset($_SESSION['nivel']) || $_SESSION['nivel'] != 1) {
    // Redirecionar para a página de erro se o usuário não for administrador
    header("Location: /404.php");
    exit();
}

// Regenerar o ID da sessão periodicamente para evitar ataques de fixação de sessão
if (!isset($_SESSION['last_regenerated'])) {
    $_SESSION['last_regenerated'] = time();
} elseif (time() - $_SESSION['last_regenerated'] > 600) { // Regenerar a cada 10 minutos
    session_regenerate_id(true);
    $_SESSION['last_regenerated'] = time();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Área de administração para gerir utilizadores e salas.">
    <meta name="robots" content="noindex, nofollow"> <!-- Evita indexação por motores de busca -->
    <title>Área de Administração</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #4CAF50, #2a9d8f);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .admin-container {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .admin-container h1 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #4CAF50;
        }

        .admin-container p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #666;
        }

        .admin-container button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .admin-container button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .admin-container button:active {
            background-color: #003f7f;
        }

        .admin-container button + button {
            margin-top: 10px;
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Bem-vindo à Área de Administração</h1>
        <button onclick="location.href='/resources/Admin-resources/consultar.php'">Gerir Utilizadores</button>
        <button onclick="location.href='/resources/Admin-resources/adicionar_sala.html'">Adicionar Sala</button>
        <button onclick="location.href='/resources/Admin-resources/gerir_salas.php'">Gerir Salas</button>
        <button onclick="location.href='/index.php'">Voltar ao Início</button>
    </div>
</body>
</html>
