<?php
include_once "config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userPhoto = (!empty($user['profile_picture']) && file_exists($user['profile_picture']))
    ? $user['profile_picture']
    : "uploads/profile_pictures/default-profile.jpeg";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>Blood Place Voluntary</title>
    <style>
        body {
            font-family: 'Aptos', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        .header-bloodplace {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #D11A1A;
            padding: 15px 15px;
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }


        .logo-bloodplace {
            font-size: 1.5rem;
        }

        .logo-link-bloodplace {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .menu-btn-bloodplace {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .menu-line-bloodplace {
            width: 30px;
            height: 3px;
            background-color: white;
            margin: 4px 0;
            transition: 0.4s;
        }

        .nav-bloodplace {
            display: flex;
            gap: 20px;
        }

        .nav-bloodplace a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav-bloodplace a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .cta-button-bloodplace {
            background-color: white;
            color: #D11A1A;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .profile-link-bloodplace {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
        }

        .profile-img-bloodplace {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .menu-btn-bloodplace.open .menu-line-bloodplace:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .menu-btn-bloodplace.open .menu-line-bloodplace:nth-child(2) {
            opacity: 0;
        }

        .menu-btn-bloodplace.open .menu-line-bloodplace:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        .menu-line-bloodplace {
            transition: 0.4s;
        }


        @media (max-width: 768px) {
            .header-bloodplace {
                padding: 5px 5px;
            }
        }

        @media (max-width: 768px) {
            .menu-btn-bloodplace {
                display: flex;
            }

            .nav-bloodplace {
                display: none;
                flex-direction: column;
                background: #D11A1A;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
            }

            .nav-bloodplace.active {
                display: flex;
            }
        }

        .logo-img {
            height: 50px;
            width: auto;
        }

        .notification-badge {
            background-color: #e74c3c;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 3px 7px;
            border-radius: 50%;
            position: relative;
            top: -10px;
            left: -5px;
        }
    </style>
</head>

<body>
    <header class="header-bloodplace">
        <div class="logo-bloodplace">
            <a href="index.php" class="logo-link-bloodplace">
                <img src="images/Banco de Sangue Branco.png" alt="Blood Place Voluntary" class="logo-img">
            </a>
        </div>

        <div class="menu-btn-bloodplace">
            <div class="menu-line-bloodplace"></div>
            <div class="menu-line-bloodplace"></div>
            <div class="menu-line-bloodplace"></div>
        </div>
        <nav class="nav-bloodplace">
            <a href="index.php"><i class="fas fa-home"></i> Início</a>
            <a href="about.php"><i class="fas fa-info-circle"></i> Sobre Nós</a>
            <a href="services.php"><i class="fas fa-hand-holding-heart"></i> Serviços</a>
            <a href="testimonials.php"><i class="fas fa-comments"></i> Depoimentos</a>
            <a href="contact.php"><i class="fas fa-phone-alt"></i> Contato</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="schedule_donation.php"><i class="fas fa-calendar-alt"></i> Agendar</a>
                <a href="reservar.php"><i class="fas fa-box"></i> Reservar</a>
                <?php

                $notifQuery = $conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE to_user_id = :user_id AND is_read = 0");
                $notifQuery->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $notifQuery->execute();
                $notifCount = $notifQuery->fetch(PDO::FETCH_ASSOC)['total'];
                ?>
                <a href="notifications.php">
                    <i class="fas fa-bell"></i> Notificações
                    <?php if ($notifCount > 0): ?>
                        <span id="notification-count" class="notification-badge"><?php echo $notifCount; ?></span>
                    <?php endif; ?>
                </a>


                <a href="profile.php" class="profile-link-bloodplace">
                    <img src="<?php echo htmlspecialchars($userPhoto, ENT_QUOTES); ?>" alt="Perfil" class="profile-img-bloodplace">
                    <?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES); ?>
                </a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login/Registro</a>
            <?php endif; ?>
        </nav>
    </header><br><br>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.querySelector('.menu-btn-bloodplace');
            const nav = document.querySelector('.nav-bloodplace');

            menuBtn.addEventListener('click', () => {
                nav.classList.toggle('active');
                menuBtn.classList.toggle('open');
            });
        });
    </script>
</body>

</html>