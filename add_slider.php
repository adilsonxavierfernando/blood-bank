<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$host = 'localhost';
$dbname = 'blood_place';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $imagem = $_FILES['imagem']['name'];
        $uploadDir = "uploads/";

        $caminhoImagem = $uploadDir . basename($imagem);
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoImagem)) {
            $stmt = $pdo->prepare("INSERT INTO slider_images (caminho_imagem, titulo, descricao) VALUES (?, ?, ?)");
            $stmt->execute([$caminhoImagem, $titulo, $descricao]);

            $mensagem = "Imagem e informações inseridas com sucesso!";
        } else {
            $mensagem = "Erro ao fazer upload da imagem.";
        }
    }
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Imagem ao Slider - Painel Admin</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f0f0;
            color: #333;
        }

        .admin-dashboard {
            display: grid;
            grid-template-columns: 250px auto;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #D11A1A;
            padding: 20px;
            color: #fff;
        }

        .sidebar h2 {
            text-align: center;
        }

        .menu {
            list-style: none;
            margin-top: 20px;
        }

        .menu li {
            margin: 15px 0;
        }

        .menu a {
            color: #b0b0b0;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .menu a:hover {
            background-color: #8f1a1a;
            color: white;
        }

        .menu a i {
            margin-right: 10px;
        }

        .content {
            background: url('uploads/slider2.jpg') no-repeat center center/cover;
            background-color: rgba(255, 255, 255, 0.8);
            background-blend-mode: lighten;
            padding: 30px;
        }

      
        form {
            max-width: 600px;
            margin: auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        form label {
            font-weight: bold;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h1 {
            text-align: center;
        }

        form button {
            background-color: #D11A1A;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        form button:hover {
            background-color: #8f1a1a;
        }

        .mensagem {
            text-align: center;
            margin: 20px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="admin-dashboard">
    <aside class="sidebar">
            <h2>Painel Admin</h2>
            <ul class="menu">
                <li><a href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a href="add_campaign.php"><i class="bi bi-plus-circle"></i> Adicionar Campanha</a></li>
                <li><a href="view_campaigns.php"><i class="bi bi-list"></i> Ver Campanhas</a></li>
                <li><a href="view_donors.php"><i class="bi bi-people"></i> Doadores</a></li>
                 <li><a href="view_donations.php"><i class="bi bi-droplet"></i>  Doações Agendadas</a></li>
                <li><a href="view_stock_donations.php"><i class="bi bi-box"></i> Estoque</a></li>
                <li><a href="view_message.php"><i class="bi bi-envelope-fill"></i> Mensagens</a></li>
                <li><a href="view_reservations.php"><i class="bi bi-calendar-check"></i> Reservas</a></li>
                <li><a href="add_slider.php"><i class="bi bi-images"></i> Adicionar Slide</a></li>
                <li><a href="manage_sliders.php"><i class="bi bi-sliders"></i> Gerenciar Slides</a></li>
                <li><a href="admin_logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
            </ul>

        </aside>

        <main class="content">
            <h1>Adicionar Imagem ao Slider</h1>
            <?php if (isset($mensagem)) {
                echo "<div class='mensagem'>{$mensagem}</div>";
            } ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" required>

                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" required></textarea>

                <label for="imagem">Selecionar Imagem:</label>
                <input type="file" name="imagem" id="imagem" accept="image/*" required>

                <button type="submit">Salvar</button>
            </form>
        </main>
    </div>
</body>

</html>