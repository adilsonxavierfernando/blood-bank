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


    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM slider_images WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_sliders.php");
        exit;
    }


    $stmt = $pdo->query("SELECT * FROM slider_images");
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Slides</title>
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

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .sly {
            background-color: #D11A1A;
            color: white;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            font-weight: 600;
        }

        .edit {
            background: #3498db;
            color: white;
        }

        .delete {
            background: #D11A1A;
            color: white;
        }

        .delete:hover {
            background: #8f1a1a;
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
            <h1>Gerenciar Slides</h1>
            <div class="container">

                <table>
                    <tr class="sly">
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Ação</th>
                    </tr>
                    <?php foreach ($sliders as $slide): ?>
                        <tr>
                            <td><img src="<?= $slide['caminho_imagem']; ?>" alt="Slide"></td>
                            <td><?= htmlspecialchars($slide['titulo']); ?></td>
                            <td><?= htmlspecialchars($slide['descricao']); ?></td>
                            <td class="actions">

                                <a href="?delete=<?= $slide['id']; ?>" class="delete" onclick="return confirm('Tem certeza que deseja excluir este slide?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </main>
    </div>
</body>

</html>