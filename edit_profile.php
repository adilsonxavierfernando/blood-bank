<?php
session_start();

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

    $stmt = $pdo->prepare("SELECT name, email, birthdate, blood_type, city, profile_picture, gender FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Usuário não encontrado.";
        exit;
    }

    if (isset($_POST['upload_picture']) && isset($_FILES['profile_picture'])) {
        $uploadDir = "uploads/profile_pictures/";
        $fileName = basename($_FILES['profile_picture']['name']);
        $targetFilePath = $uploadDir . uniqid() . "_" . $fileName;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
                $stmt->bindParam(':profile_picture', $targetFilePath);
                $stmt->bindParam(':id', $_SESSION['user_id']);
                $stmt->execute();

                $_SESSION['success_message'] = "Foto de perfil atualizada com sucesso!";
            } else {
                $_SESSION['error_message'] = "Erro ao fazer upload da imagem.";
            }
        } else {
            $_SESSION['error_message'] = "Formato de imagem não permitido. Use JPG, PNG ou GIF.";
        }
        header("Location: edit_profile.php");
        exit;
    }

    $today = new DateTime();
    $maxDate = (clone $today)->sub(new DateInterval('P18Y'))->format('Y-m-d');
    $minDate = (clone $today)->sub(new DateInterval('P69Y'))->format('Y-m-d');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_profile'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $birthdate = $_POST['birthdate'];
            $blood_type = $_POST['blood_type'];
            $city = $_POST['city'];
            $gender = $_POST['gender'];  

            $birthdateObj = new DateTime($birthdate);
            $age = $today->diff($birthdateObj)->y;

            if ($age < 18) {
                $_SESSION['error_message'] = "Você precisa ter pelo menos 18 anos para doar sangue!";
                header("Location: edit_profile.php");
                exit;
            }

            if ($age > 69) {
                $_SESSION['error_message'] = "Doadores não podem ter mais de 69 anos!";
                header("Location: edit_profile.php");
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, birthdate = :birthdate, blood_type = :blood_type, city = :city, gender = :gender WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':blood_type', $blood_type);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();

            $_SESSION['success_message'] = "Perfil atualizado com sucesso!";
            header("Location: edit_profile.php");
            exit;
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
    <title>Editar Perfil - Blood Place Voluntary</title>
    <link rel="stylesheet" href="edit_profile.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap">
    <style>
        .profile-section img {
            display: block;
            margin: 10px auto;
            border-radius: 50%;
            border: 4px solid #721c24;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }
    </style>
</head>

<body>
    <?php include_once "header.php" ?>

    <section class="edit-profile-container">
        <h2>Editar Perfil</h2>
        <div class="profile-section">
            <h3>Alterar Foto de Perfil</h3>
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'uploads/default_profile.png'); ?>" alt="Foto de Perfil" width="150" height="150"><br>

            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <label for="profile_picture">Selecionar nova foto:</label>
                <input type="file" id="profile_picture" name="profile_picture" required>
                <button type="submit" name="upload_picture">Atualizar Foto</button>
            </form>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error">
                <?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="profile-sections">
            <form action="upload.php" method="POST" enctype="multipart/form-data">


            </form>
            <div class="profile-section">

                <h3>Informações Pessoais</h3>
                <form action="edit_profile.php" method="POST">
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                    <label for="birthdate">Data de Nascimento:</label>
                    <input type="date" id="birthdate" name="birthdate"
                        value="<?php echo htmlspecialchars($user['birthdate']); ?>"
                        min="<?php echo $minDate; ?>"
                        max="<?php echo $maxDate; ?>"
                        required>

                    <label for="blood_type">Tipo Sanguíneo:</label>
                    <select id="blood_type" name="blood_type" required>
                        <option value="A+" <?php echo $user['blood_type'] === 'A+' ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?php echo $user['blood_type'] === 'A-' ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?php echo $user['blood_type'] === 'B+' ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?php echo $user['blood_type'] === 'B-' ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?php echo $user['blood_type'] === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?php echo $user['blood_type'] === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                        <option value="O+" <?php echo $user['blood_type'] === 'O+' ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?php echo $user['blood_type'] === 'O-' ? 'selected' : ''; ?>>O-</option>
                    </select>

                    <label for="city">Cidade:</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                    <label for="gender">Gênero:</label>
                    <select id="gender" name="gender" required>
                        <option value="Masculino" <?php echo $user['gender'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Feminino" <?php echo $user['gender'] === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                        <option value="Outro" <?php echo $user['gender'] === 'Outro' ? 'selected' : ''; ?>>Outro</option>
                    </select>
                    <button type="submit" name="update_profile" class="btn">Salvar Alterações</button>
                </form>
            </div>

            <div class="profile-section">
                <h3>Alterar Senha</h3>
                <form action="edit_profile.php" method="POST">
                    <label for="current_password">Senha Atual:</label>
                    <input type="password" id="current_password" name="current_password" required>

                    <label for="new_password">Nova Senha:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirmar Nova Senha:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit" name="change_password" class="btn">Alterar Senha</button>
                </form>
            </div>


            <div class="profile-section">
                <h3>Preferências</h3>
                <form action="edit_profile.php" method="POST">
                    <label>
                        <input type="checkbox" name="receive_emails" checked> Receber emails sobre campanhas
                    </label>
                    <label>
                        <input type="checkbox" name="receive_notifications" checked> Receber notificações
                    </label>
                    <button type="submit" name="update_preferences" class="btn">Salvar Preferências</button>
                </form>
            </div>
        </div>
    </section>

    <?php include_once "footer.php" ?>
</body>

</html>
