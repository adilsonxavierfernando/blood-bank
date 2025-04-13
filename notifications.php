<?php
session_start();
include 'config.php'; 
include 'funcoes.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$queryTipoSanguineo = $conn->prepare("SELECT blood_type FROM users WHERE id = :user_id");
$queryTipoSanguineo->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$queryTipoSanguineo->execute();
$userBloodType = $queryTipoSanguineo->fetchColumn();

if ($userBloodType && verificarEstoque($userBloodType, $conn)) {
    criarNotificacaoFaltaEstoque($userBloodType, $conn);
}


$updateRead = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE to_user_id = :user_id");
$updateRead->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$updateRead->execute();

verificarDataAgendada($conn);

try {
   
    $query = "SELECT n.id, n.type, n.message, n.created_at, u.name, u.profile_picture 
              FROM notifications n
              LEFT JOIN users u ON n.from_user_id = u.id
              WHERE n.to_user_id = :user_id 
              ORDER BY n.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar notificações: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações - Blood Place Voluntary</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .notifications-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .notifications-container h2 {
            color: #d32f2f;
            text-align: center;
        }

        .notifications-list {
            list-style: none;
            padding: 0;
        }

        .notification-item {
            display: flex;
            align-items: center;
            background: #fff3f3;
            border-left: 5px solid #d32f2f;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .notification-item p {
            margin: 5px 0;
        }

        .notification-date {
            font-size: 12px;
            color: #777;
        }

        .no-notifications {
            text-align: center;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?><br><br><br>
    <div class="notifications-container">
        <h2>Notificações</h2>
        <div class="notifications-list">
            <?php if (empty($notifications)): ?>
                <p class="no-notifications">Você não tem novas notificações.</p>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item">
                        <img src="<?= htmlspecialchars($notification['profile_picture'] ?? 'images/default-profile.png'); ?>" alt="Perfil" class="profile-pic">
                        <div>
                            <p>
                                <strong><?= htmlspecialchars($notification['name'] ?? 'Hemoterapia do Hospital Geral Do Moxico'); ?></strong>
                                <?= htmlspecialchars($notification['message']); ?>
                                <a href="<?= htmlspecialchars($notification['link'] ?? 'schedule_donation.php'); ?>" style="color: #d32f2f; font-weight: bold;"> Agendar Doação</a>
                            </p>
                            <span class="notification-date"><?= date("d/m/Y H:i", strtotime($notification['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <?php include 'footer.php'; ?>
    
    <script>
        function fetchNotifications() {
            fetch('fetch_notifications.php')
                .then(response => response.json())
                .then(data => {
                    let container = document.querySelector('.notifications-list');
                    container.innerHTML = '';
                    
                    if (data.length === 0) {
                        container.innerHTML = '<p class="no-notifications">Você não tem novas notificações.</p>';
                    } else {
                        data.forEach(notification => {
                            let notificationHTML = `
                                <div class="notification-item">
                                    <img src="${notification.profile_picture ?? 'images/logotipobpv.png'}" alt="Perfil" class="profile-pic">
                                    <div>
                                        <p>
                                            <strong>${notification.name ?? 'Hemoterapia do Hospital Geral Do Moxico'}</strong> 
                                            ${notification.message} 
                                            <a href="${notification.link ?? 'schedule_donation.php'}" style="color: #d32f2f; font-weight: bold;">Agendar Doação</a>
                                        </p>
                                        <span class="notification-date">${new Date(notification.created_at).toLocaleString()}</span>
                                    </div>
                                </div>
                            `;
                            container.innerHTML += notificationHTML;
                        });
                    }
                })
                .catch(error => console.error('Erro ao carregar notificações:', error));
        }
        
        setInterval(fetchNotifications, 10000);
        fetchNotifications();
    </script>
</body>
</html>