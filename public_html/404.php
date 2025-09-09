<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 404 - Página Não Encontrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg,rgb(236, 21, 21),rgb(236, 21, 21));
            color: #333;
        }

        .error-container {
            text-align: center;
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .error-container h1 {
            font-size: 48px;
            color:rgb(236, 21, 21);
        }

        .error-container p {
            font-size: 18px;
            margin: 20px 0;
            color: #555;
        }

        .error-container a {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background-color:rgb(236, 21, 21);
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .error-container a:hover {
            background-color:rgb(138, 8, 8);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Erro 404</h1>
        <p>A página não foi encontrada.</p>
        <a href="/index.php">Voltar para a Página Inicial</a>
    </div>
</body>
</html>
