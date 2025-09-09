<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Salas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1em 2em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header a {
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        header a:hover {
            color: #4CAF50;
            background-color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .logo {
            font-size: 24px;
        }

        .nav-menu li {
            display: inline;
            margin-left: 15px;
        }

        .top-section {
            display: flex;
            min-height: 100vh;
            align-items: center;
        }

        .left-section, .right-section {
            flex: 1;
            padding: 2em;
        }

        .left-section {
            background-color: #f4f4f4;
            text-align: center;
        }

        .left-section h1 {
            font-size: 2.5em;
            color: #333;
        }

        .left-section p {
            font-size: 1.2em;
            color: #666;
        }

        .right-section {
            background: url('https://www.smartouch.com.br/wp-content/uploads/2018/07/1649_20110706181008_FOTO_ALBUM_Sala-de-Reuni%C3%A3o.jpg') no-repeat center center;
        background-size: cover; /* Garante que a imagem preencha o espaço sem distorção */
        height: 100%; /* Altura completa para corresponder à seção */
        min-height: 400px;
        }

        .button-section {
            text-align: center;
            margin: 2em 0;
        }

        .button-section a {
            text-decoration: none;
            padding: 1em 2em;
            background-color: #4CAF50;
            color: white;
            font-size: 1.2em;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button-section a:hover {
            background-color: #3e8e41;
        }

        .carousel {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            width: 90%;
            max-width: 800px;
            margin: 2em auto;
            overflow: hidden;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .carousel-images {
            display: flex;
            transition: transform 0.3s ease-in-out;
        }

        .carousel-images img {
            width: 100%;
            flex-shrink: 0;
        }

        .carousel-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            font-size: 2em;
            cursor: pointer;
            z-index: 10;
        }

        .carousel-button.left {
            left: 10px;
        }

        .carousel-button.right {
            right: 10px;
        }

        footer {
            text-align: center;
            padding: 1em 0;
            background-color: #f4f4f4;
            color: #666;
        }
    </style>
</head>
<body>
<header>
        <div class="logo">
            <!-- Escapamos o conteúdo para evitar Cross-Site Scripting (XSS) -->
            <a href="index.php"><?php echo htmlspecialchars("Gestão de Salas", ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
        <ul class="nav-menu">
            <?php if (isset($_SESSION['numero'])): ?>
                <li>
                    <!-- Escapamos os URLs e o texto para evitar XSS -->
                    <a href="/resources/users/loginForm/perfil.php"><?php echo htmlspecialchars("Perfil", ENT_QUOTES, 'UTF-8'); ?></a>
                </li>
                <?php if (isset($_SESSION['nivel']) && $_SESSION['nivel'] == 1): ?>
                    <li>
                        <a href="/admin.php"><?php echo htmlspecialchars("Administração", ENT_QUOTES, 'UTF-8'); ?></a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="/resources/users/loginForm/logout.php"><?php echo htmlspecialchars("Logout", ENT_QUOTES, 'UTF-8'); ?></a>
                </li>
            <?php else: ?>
                <li>
                    <a href="/resources/users/loginForm/login.html"><?php echo htmlspecialchars("Login", ENT_QUOTES, 'UTF-8'); ?></a>
                </li>
                <li>
                    <a href="/resources/users/RegisterForm/formulário.html"><?php echo htmlspecialchars("Registrar", ENT_QUOTES, 'UTF-8'); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </header>

    <div class="top-section">
        <div class="left-section">
            <h1><?php echo htmlspecialchars("Bem-vindo à Gestão de Salas", ENT_QUOTES, 'UTF-8'); ?></h1>
            <p><?php echo htmlspecialchars("Organize suas salas de forma prática e eficiente.", ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="right-section"></div>
    </div>

    <div class="button-section">
        <!-- Garantimos a segurança no link para evitar XSS -->
        <a href="/resources/salas/visualizar_salas.php"><?php echo htmlspecialchars("Ver Salas Disponíveis", ENT_QUOTES, 'UTF-8'); ?></a>
    </div>

    <div class="carousel" id="salas">
        <button class="carousel-button left" onclick="prevImage()">&#8249;</button>
        <div class="carousel-images">
            <!-- Escapamos as URLs e o texto alternativo -->
            <img src="<?php echo htmlspecialchars("https://www.gaiacultura.pt/media/g4tdspk3/2017-09-09-inaugura%C3%A7%C3%A3o-audit%C3%B3rio-88.jpg", ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars("Sala 1", ENT_QUOTES, 'UTF-8'); ?>">
            <img src="<?php echo htmlspecialchars("https://www.cise.pt/pt/images/CISE/3-servicos-e-equipamentos/22-saladeformacao.jpg", ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars("Sala 2", ENT_QUOTES, 'UTF-8'); ?>">
            <img src="<?php echo htmlspecialchars("https://cdn.eurekacoworking.com/wp-content/uploads/2020/10/02143729/escritorio-paulista-1.jpg", ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars("Sala 3", ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button class="carousel-button right" onclick="nextImage()">&#8250;</button>
    </div>

    <footer>
        <!-- Escapamos o texto do rodapé -->
        &copy; <?php echo htmlspecialchars("2024 Gestão de Salas. Todos os direitos reservados.", ENT_QUOTES, 'UTF-8'); ?>
    </footer>

    <script>
        let currentIndex = 0;

        function showImage(index) {
            const images = document.querySelector('.carousel-images');
            const totalImages = images.children.length;
            if (index >= totalImages) {
                currentIndex = 0;
            } else if (index < 0) {
                currentIndex = totalImages - 1;
            } else {
                currentIndex = index;
            }
            images.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        function nextImage() {
            showImage(currentIndex + 1);
        }

        function prevImage() {
            showImage(currentIndex - 1);
        }
    </script>
</body>
</html>