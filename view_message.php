<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}


$host = 'localhost';
$dbname = 'blood_place';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}


$query = "SELECT * FROM contatos ORDER BY data_envio DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens dos Usuários - Blood Place Voluntary</title>
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
            display: grid;
            grid-template-columns: 250px auto;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #D11A1A;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .menu {
            list-style: none;
            padding: 0;
        }

        .menu li {
            margin: 15px 0;
        }

        .menu a {
            color: #b0b0b0;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .menu a:hover {
            background-color: #8f1a1a;
            color: white;
        }

        .menu a i {
            margin-right: 10px;
            font-size: 1.3rem;
        }

        .content {
            background: url('uploads/slider2.jpg') no-repeat center center/cover;
            background-color: rgba(255, 255, 255, 0.8);
            background-blend-mode: lighten;
            padding: 30px;
        }

        h1 {
            font-size: 2rem;
            color: #27293d;
            text-align: center;
            margin-bottom: 30px;
        }

        .message-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .message-table th, .message-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .message-table th {
            background: #D11A1A;
            color: white;
        }

        .message-table tr:hover {
            background: #f4f4f4;
        }

        .no-messages {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
            color: #666;
        }

        @media (max-width: 768px) {
            body {
                display: block;
            }
            .sidebar {
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>

<body>
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


    <div class="content">
        <h1>Mensagens dos Usuários</h1>

        <?php if (count($messages) > 0): ?>
            <table class="message-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Mensagem</th>
                        <th>Data de Envio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= htmlspecialchars($message['id']); ?></td>
                            <td><?= htmlspecialchars($message['nome']); ?></td>
                            <td><?= htmlspecialchars($message['email']); ?></td>
                            <td><?= nl2br(htmlspecialchars($message['mensagem'])); ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($message['data_envio'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-messages">Nenhuma mensagem recebida até o momento.</p>
        <?php endif; ?>
    </div>
</body>

</html>