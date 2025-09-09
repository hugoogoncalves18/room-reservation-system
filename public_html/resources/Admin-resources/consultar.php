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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Utilizadores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        h1 {
            margin: 20px 0;
            color: #333;
        }

        table {
            width: 90%;
            max-width: 1200px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            color: #333;
        }

        td:last-child {
            text-align: center;
        }

        form {
            display: inline-block;
        }

        input[type="submit"] {
            padding: 8px 16px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        input[type="submit"]:active {
            background-color: #003f7f;
        }

        .no-results {
            font-size: 18px;
            color: #666;
            margin: 20px 0;
        }

        .back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        footer {
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>Lista de Utilizadores</h1>
    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Número</th>
                    <th>Nome</th>
                    <th>Login</th>
                    <th>Nível</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>";
        // Exibir dados
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["numero"]) . "</td>
                    <td>" . htmlspecialchars($row["nome"]) . "</td>
                    <td>" . htmlspecialchars($row["login"]) . "</td>
                    <td>" . htmlspecialchars($row["nivel"]) . "</td>
                    <td>" . htmlspecialchars($row["email"]) . "</td>
                    <td>
                        <form action='editar.php' method='get'>
                            <input type='hidden' name='numero' value='" . htmlspecialchars($row["numero"]) . "'>
                            <input type='submit' value='Alterar'>
                        </form>
                        <form action='eliminar.php' method='post' onsubmit=\"return confirm('Tem a certeza que deseja eliminar este utilizador?');\">
                            <input type='hidden' name='numero' value='" . htmlspecialchars($row["numero"]) . "'>
                            <input type='submit' value='Eliminar'>
                        </form>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-results'>Nenhum utilizador encontrado.</div>";
    }
    $conn->close();
    ?>
    <!-- Botão para voltar ao admin.php -->
    <a href="/admin.php" class="back-btn">Voltar ao Dashboard</a>
    <footer>&copy; 2024 Gestão de Salas</footer>
</body>
</html>
