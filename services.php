<?php
include_once 'configu.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : 'uploads/profile_pictures/default-profile.jpeg';
} else {

    $_SESSION['profile_picture'] = 'uploads/profile_pictures/default-profile.jpeg';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - Blood Place Voluntary</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Aptos', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .unique-services-header {
            background: linear-gradient(to right, rgba(209, 26, 26, 0.9), rgba(50, 50, 50, 0.7));
            color: white;
            text-align: center;
            padding: 80px 20px;
        }

        .unique-services-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            padding: 40px;
            background-color: #f9f9f9;
            max-width: 1200px;
            margin: 0 auto;
        }

        .unique-service-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .unique-service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .unique-service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .unique-service-card-content {
            padding: 20px;
        }

        .unique-service-card-content h2 {
            font-size: 1.5rem;
            color: #d91e18;
            margin-bottom: 10px;
        }

        .unique-service-card-content p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
        }

        .unique-cta-button {
            display: inline-block;
            background-color: #d91e18;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .unique-cta-button:hover {
            background-color: #b3130d;
            transform: translateY(-3px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .unique-services-header {
                padding: 60px 15px;
            }

            .unique-services-container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include_once "header.php"; ?><br>

    <header class="unique-services-header">
        <h1><i class="bi bi-hand-thumbs-up"></i> Nossos Serviços</h1>
        <p>Juntos fazemos a diferença com a doação de sangue</p>
    </header>

    <section class="unique-services-container">
        <article class="unique-service-card">
            <img src="uploads/slider1.jpg" alt="Doação de Sangue">
            <div class="unique-service-card-content">
                <h2>Doação de Sangue</h2>
                <p>Ajude a salvar vidas ao doar sangue em nossos centros credenciados.</p>
                <a href="schedule_donation.php" class="unique-cta-button">Agendar Doação</a>
            </div>
        </article>

        <article class="unique-service-card">
            <img src="uploads/slider2.jpg" alt="Campanhas de Conscientização">
            <div class="unique-service-card-content">
                <h2>Campanhas de Conscientização</h2>
                <p>Participe das nossas ações para promover a importância da doação de sangue.</p>
                <a href="events.php" class="unique-cta-button">Saiba Mais</a>
            </div>
        </article>

        <article class="unique-service-card">
            <img src="uploads/slider3.jpg" alt="Reserva de Sangue">
            <div class="unique-service-card-content">
                <h2>Reserva de Sangue</h2>
                <p>Garantimos a disponibilidade de sangue para situações de emergência.</p>
                <a href="reservar.php" class="unique-cta-button">Reservar Agora</a>
            </div>
        </article>

        <article class="unique-service-card">
            <img src="uploads/OIP (47).jpeg" alt="Suporte Comunitário">
            <div class="unique-service-card-content">
                <h2>Suporte Comunitário</h2>
                <p>Trabalhamos com comunidades para melhorar o acesso à doação de sangue.</p>
                <a href="contact.php" class="unique-cta-button">Entre em Contato</a>
            </div>
        </article>
    </section>

    <?php include_once "footer.php"; ?>
</body>

</html>
