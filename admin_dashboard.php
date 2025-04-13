<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "blood_place";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$totalCampaignsQuery = "SELECT COUNT(*) AS total_campaigns FROM campanhas";
$totalDonorsQuery = "SELECT COUNT(DISTINCT doador_id) AS total_donors FROM doacoes";
$scheduledDonationsQuery = "SELECT COUNT(*) AS scheduled_donations FROM doacoes WHERE data_agendada >= CURDATE()";
$totalQuantityDonatedQuery = "SELECT SUM(quantidade) AS total_quantity FROM doacoes WHERE data <= CURDATE()";

$totalCampaigns = $conn->query($totalCampaignsQuery)->fetch_assoc()['total_campaigns'] ?? 0;
$totalDonors = $conn->query($totalDonorsQuery)->fetch_assoc()['total_donors'] ?? 0;
$scheduledDonations = $conn->query($scheduledDonationsQuery)->fetch_assoc()['scheduled_donations'] ?? 0;
$totalQuantityDonated = $conn->query($totalQuantityDonatedQuery)->fetch_assoc()['total_quantity'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador - Blood Place</title>
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
            font-size: 1.5rem;
            text-align: center;
        }

        .menu {
            list-style: none;
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .menu li {
            margin: 15px 0;
        }

        .menu a {
            color: #fff;
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


        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: #D11A1A;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s;
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .logo-bloodplace {
            margin-left: 31%;
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
            <h1>Bem-vindo ao Painel do Administrador</h1>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total de Campanhas</h3>
                    <p><?php echo $totalCampaigns; ?> campanhas cadastradas</p>
                </div>

                <div class="card">
                    <h3>Total de Doadores</h3>
                    <p><?php echo $totalDonors; ?> doadores registrados</p>
                </div>

                <div class="card">
                    <h3>Doações Agendadas</h3>
                    <p><?php echo $scheduledDonations; ?> doações agendadas</p>
                </div>


            </div>
            <div class="logo-bloodplace">
                <a href="admin_dashboard.php" class="logo-link-bloodplace">
                    <img src="images/logotipobpv.png" alt="Blood Place Voluntary" class="logo-img">
                </a>
            </div>
        </main>
    </div>
</body>

</html>