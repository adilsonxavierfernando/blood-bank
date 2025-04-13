<?php
session_start();
include_once "header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'blood_place';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT name, email, birthdate, blood_type, city, profile_picture, cover_picture FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Usuário não encontrado.";
        exit;
    }

  
    $profilePicture = !empty($user['profile_picture']) && file_exists("uploads/profile_pictures/{$user['profile_picture']}")
        ? "uploads/profile_pictures/{$user['profile_picture']}"
        : "default_profile.jpg";

    $coverPicture = !empty($user['cover_picture']) && file_exists("uploads/cover_pictures/{$user['cover_picture']}")
        ? "uploads/cover_pictures/{$user['cover_picture']}"
        : "default_cover.jpg";

} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Perfil - Blood Place Voluntary</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap">
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
        }

        .share-profile-container {
            padding: 40px;
            text-align: center;
        }

        .profile-card {
            background-color: white;
            max-width: 500px;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .cover-photo {
            height: 150px;
            background-size: cover;
            background-position: center;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            background-size: cover;
            background-position: center;
            border-radius: 50%;
            margin: -60px auto 10px;
            border: 5px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .profile-info {
            padding: 20px;
            text-align: center;
        }

        .profile-info h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .profile-info p {
            font-size: 16px;
            margin: 5px 0;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }

        .btn {
            padding: 10px 20px;
            background-color: #D11A1A;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #D11A1A;
        }

        .profile-link {
            margin-top: 20px;
        }

        .profile-link input {
            width: 80%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .copy-success {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
<br><br><br><br><br><br><br>
    <section class="share-profile-container">
        <h2>Compartilhar Perfil</h2>

        <div class="profile-card">
        <div class="cover-photo" style="background-image: url('<?php echo $coverPicture; ?>');"></div>
            <div class="profile-photo" style="background-image: url('<?php echo $profilePicture; ?>');"></div>
            <div class="profile-info">
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><strong>Tipo Sanguíneo:</strong> <?php echo htmlspecialchars($user['blood_type']); ?></p>
                <p><strong>Cidade:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Data de Nascimento:</strong> <?php echo date("d/m/Y", strtotime($user['birthdate'])); ?></p>
            </div>
        </div>

        <div class="share-options">
            <h3>Compartilhar em:</h3>
            <div class="social-links">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://bloodplacevoluntary.com/profile.php?id=" . $_SESSION['user_id']); ?>" target="_blank" class="btn">Facebook</a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("http://bloodplacevoluntary.com/profile.php?id=" . $_SESSION['user_id']); ?>&text=<?php echo urlencode("Confira meu perfil no Blood Place Voluntary!"); ?>" target="_blank" class="btn">Twitter</a>
                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode("Confira meu perfil no Blood Place Voluntary: http://bloodplacevoluntary.com/profile.php?id=" . $_SESSION['user_id']); ?>" target="_blank" class="btn">WhatsApp</a>
            </div>
        </div>

        <div class="profile-link">
            <h3>Link do Perfil:</h3>
            <input type="text" id="profile-link" value="http://bloodplacevoluntary.com/profile.php?id=<?php echo $_SESSION['user_id']; ?>" readonly>
            <button onclick="copyProfileLink()" class="btn">Copiar Link</button>
            <p id="copy-success" class="copy-success" style="display: none;">Link copiado para a área de transferência!</p>
        </div>
    </section>

    <?php include_once "footer.php" ?>

    <script>
        function copyProfileLink() {
            const linkInput = document.getElementById('profile-link');
            const successMessage = document.getElementById('copy-success');
            linkInput.select();
            document.execCommand('copy');
            successMessage.style.display = 'block';
            setTimeout(() => successMessage.style.display = 'none', 3000);
        }
    </script>
</body>

</html>