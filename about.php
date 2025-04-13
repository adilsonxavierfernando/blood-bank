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
    <title>Sobre Nós - Blood Place Voluntary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
     <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Aptos', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        header {
            background: #ff4d4d;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav {
            display: flex;
            gap: 1rem;
        }

        .nav a {
            text-decoration: none;
            color: white;
            font-weight: 500;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        .services-header {
            background: linear-gradient(to right, rgba(209, 26, 26, 0.9), rgba(50, 50, 50, 0.7));
            color: white;
            text-align: center;
            padding: 80px 20px;
        }

        .mission-vision-values {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 2rem;
            background: #fff;
        }

        .card {
            background: #ffe5e5;
            padding: 1.5rem;
            margin: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            text-align: center;
        }

        .card h2 {
            color: #ff4d4d;
            margin-bottom: 1rem;
        }

        .team, .call-to-action {
            padding: 2rem;
            text-align: center;
            background: #f9f9f9;
        }

        .cta-button {
            background: #ff4d4d;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .cta-button:hover {
            background: #e04343;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .mission-vision-values {
                flex-direction: column;
                align-items: center;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .cta-button {
                font-size: 0.875rem;
            }
        }
       li{
        list-style-type: none;
       }
    </style>
</head>

<body>
<?php include_once "header.php"; ?>

<main>
    <section class="services-header">
        <h1><i class="fas fa-heart"></i> Sobre Nós</h1>
        <p>Bem-vindo ao Blood Place Voluntary, uma plataforma dedicada a conectar pessoas que querem fazer a diferença por meio da doação de sangue. Nosso propósito é salvar vidas através do altruísmo e solidariedade.</p>
    </section>

    <section class="mission-vision-values">
        <div class="card">
            <h2><i class="fas fa-bullseye"></i> Missão</h2>
            <p>Facilitar e incentivar a doação de sangue, proporcionando um sistema confiável e seguro.</p>
        </div>
        <div class="card">
            <h2><i class="fas fa-eye"></i> Visão</h2>
            <p>Ser a principal plataforma de doação de sangue em Angola, salvando milhões de vidas.</p>
        </div>
        <div class="card">
            <h2><i class="fas fa-hand-holding-heart"></i> Valores</h2>
            <ul>
                <li><i class="fas fa-check"></i> Solidariedade</li>
                <li><i class="fas fa-check"></i> Transparência</li>
                <li><i class="fas fa-check"></i> Inovação</li>
                <li><i class="fas fa-check"></i> Compromisso com a Vida</li>
            </ul>
        </div>
    </section>

    <section class="team">
        <h2><i class="fas fa-users"></i> Nossa Equipe</h2>
        <p>Contamos com uma equipe apaixonada e engajada, trabalhando todos os dias para garantir a segurança e confiabilidade do sistema. Nosso objetivo é conectar heróis doadores a quem mais precisa.</p>
    </section>

    <section class="call-to-action">
        <h2><i class="fas fa-hand-paper"></i> Junte-se a Nós!</h2>
        <p>Seja parte desse movimento transformador. Doe sangue, salve vidas e inspire outras pessoas a fazerem o mesmo.</p>
        <button class="cta-button" onclick="window.location.href='services.php'">
            <i class="fas fa-user-plus"></i> Seja um Doador
        </button>
    </section>
</main>

<?php include_once "footer.php"; ?>
</body>

</html>
