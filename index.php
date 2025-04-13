<?php
session_start();

$host = 'localhost';
$dbname = 'blood_place';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : 'uploads/profile_pictures/default-profile.jpeg';
    } else {
    
        $_SESSION['profile_picture'] = 'uploads/profile_pictures/default-profile.jpeg';
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_doadores FROM doadores");
    $stmt->execute();
    $doadores = $stmt->fetch(PDO::FETCH_ASSOC)['total_doadores'];

    $stmt = $pdo->prepare("SELECT SUM(quantidade) AS total_doacoes FROM doacoes WHERE status IN ('estoque', 'doado')");
    $stmt->execute();
    $doacoes = $stmt->fetch(PDO::FETCH_ASSOC)['total_doacoes'] ?? 0;

    $stmt = $pdo->prepare("
        SELECT d.nome, d.idade, d.email, d.foto_perfil 
        FROM doadores d 
        INNER JOIN doacoes do ON d.id = do.doador_id 
        WHERE do.status = 'estoque'
        GROUP BY d.id 
        ORDER BY SUM(do.quantidade) DESC
    ");
    $stmt->execute();
    $melhores_doadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT caminho_imagem, titulo, descricao FROM slider_images");
    $stmt->execute();
    $slider_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Place Voluntary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .notification {
            display: flex;
            align-items: center;
            justify-content: start;
            max-width: 600px;
            margin: 15px auto;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            animation: fadeIn 0.7s, slideOut 6s ease-in forwards;
            color: white;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
            text-align: left;
            gap: 12px;
        }

        .notification.success {
            background-color: #D11A1A;
        }

        .notification.error {
            background-color: #dc3545;
        }

        .notification i {
            font-size: 24px;
            flex-shrink: 0;
        }

        .icon-check-circle {
            content: "✔";
        }

        .icon-error-circle {
            content: "✖";
        }


        @keyframes fadeIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            0% {
                opacity: 1;
            }

            85% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateY(-20px);
            }
        }


        .blood-drop-effect {
            background: url('blood-drop-icon.png') no-repeat left center;
            background-size: contain;
        }

        @media (max-width: 768px) {
            .notification {
                flex-direction: column;
                font-size: 14px;
                padding: 12px;
            }
        }

        .statistics {
            display: flex;
            justify-content: space-around;
            margin: 50px 0;
            background-color: #fff5f5;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 30%;
        }

        .stat h3 {
            font-size: 20px;
            color: #D11A1A;
            margin-bottom: 10px;
        }

        .stat p {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .top-donors {
            background-color: #fff;
            padding: 40px 20px;
            margin: 30px 0;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .top-donors h2 {
            font-size: 32px;
            color: #D11A1A;
            margin-bottom: 30px;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
        }

        .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .donor img {
            margin-bottom: 10px;
            border: 2px solid #D11A1A;
        }


        .donors {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;

        }

        .donor {
            background-color: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: left;
            text-align: center;
        }

        .donor p {
            color: #333;
            font-size: 16px;
            margin: 10px 0;
        }

        .donor i {
            color: #D11A1A;
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .statistics {
                flex-direction: column;
                gap: 20px;
            }

            .stat {
                width: 100%;
            }

            .donors {
                flex-direction: column;
            }

            .donor {
                width: 100%;
            }
        }

        .logo-img {
            height: 50px;
            width: auto;
        }

        .chart-section {
            text-align: center;
            padding: 20px;
        }

        .chart-container {
            max-width: 400px;
            width: 90%;
            margin: 0 auto;
            position: relative;
        }

        .chart-section h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>

<body>
    <?php include_once "header.php" ?>

    <main>
        <br><br><br>
        <section class="slider">
            <div class="slider-content">
                <?php foreach ($slider_images as $index => $image): ?>
                    <div class="slide">
                        <img src="<?php echo htmlspecialchars($image['caminho_imagem'], ENT_QUOTES); ?>" alt="Imagem do Slider">


                        <?php if (!empty($image['titulo']) || !empty($image['descricao'])): ?>
                            <div class="slide-info">
                                <h1><i class="fas fa-image"></i> <?php echo htmlspecialchars($image['titulo'], ENT_QUOTES); ?></h1>
                                <h2><?php echo htmlspecialchars($image['descricao'], ENT_QUOTES); ?></h2>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>


                <div class="dots">
                    <?php foreach ($slider_images as $index => $image): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="prev" aria-label="Slide anterior">&laquo;</button>
            <button class="next" aria-label="Próximo slide">&raquo;</button>
        </section>
        <section id="inicio" class="hero">
            <h1><i class="fas fa-heartbeat"></i> Salve Vidas Hoje</h1>
            <p><i class="fas fa-hands-helping"></i> Junte-se a nós para transformar vidas através da doação de sangue.</p><br>
            <button class="cta-button" onclick="window.location.href='services.php'">
                <i class="fas fa-user-plus"></i> Seja um Doador
            </button>
        </section>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<div class='notification success'>
            <i class='icon-check-circle'></i>" . $_SESSION['success_message'] . "
          </div>";
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "<div class='notification error'>
            <i class='icon-error-circle'></i>" . $_SESSION['error_message'] . "
          </div>";
            unset($_SESSION['error_message']);
        }
        ?>


        <section class="statistics">
            <div class="stat">
                <section class="chart-section">
                    <h2>Distribuição das Doações</h2>
                    <div class="chart-container">
                        <canvas id="doacoesChart"></canvas>
                    </div>
                </section>
            </div>
            <div class="stat">

                <h3><i class="fas fa-tint"></i> QUANTIDADE EM ESTOQUE/ml</h3>
                <p><?php echo number_format($doacoes); ?></p>
            </div>
            <div class="stat">
                <h3><i class="fas fa-user-friends"></i> DOADORES</h3>
                <p><?php echo number_format($doadores); ?></p>
            </div>
        </section>
        <section class="top-donors">
            <h2><i class="fas fa-medal"></i> Nossos Melhores Doadores</h2>
            <div class="donors">
                <?php

                $top5_doadores = array_slice($melhores_doadores, 0, 5);

                foreach ($top5_doadores as $doador):
                ?>
                    <article class="donor">
                        <?php
                        $fotoPerfil = !empty($doador['profile_picture']) ? $doador['profile_picture'] : 'uploads/profile_pictures/default-profile.jpeg';
                        ?>
                        <img src="<?php echo $fotoPerfil; ?>" alt="Foto de <?php echo htmlspecialchars($doador['nome'], ENT_QUOTES); ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        <p><i class="fas fa-user"></i> <?php echo $doador['nome']; ?> - <?php echo $doador['idade']; ?> anos<br>
                            <i class="fas fa-envelope"></i> <?php echo $doador['email']; ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>


    </main>
    <?php include_once "footer.php" ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            let currentSlide = 0;

            function showSlide(index) {
                const sliderContent = document.querySelector('.slider-content');
                sliderContent.style.transform = `translateX(-${index * 100}%)`;

                dots.forEach((dot, i) => {
                    dot.classList.remove('active');
                    if (i === index) {
                        dot.classList.add('active');
                    }
                });
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(currentSlide);
            }

            document.querySelector('.prev').addEventListener('click', prevSlide);
            document.querySelector('.next').addEventListener('click', nextSlide);

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                });
            });

            showSlide(currentSlide);
            setInterval(nextSlide, 5000);
        });
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.querySelector('.menu-btn');
            const nav = document.querySelector('.nav');

            menuBtn.addEventListener('click', () => {
                nav.classList.toggle('active');
                menuBtn.classList.toggle('open');
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('doacoesChart').getContext('2d');
            var doacoesChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Doadores', 'Doações em Estoque'],
                    datasets: [{
                        data: [<?php echo $doadores; ?>, <?php echo $doacoes; ?>],
                        backgroundColor: ['#D11A1A', '#FFA500'],
                        borderColor: ['#B71C1C', '#FF8C00'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>