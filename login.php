<?php

ob_start();
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Place Voluntary</title>
    <link rel="stylesheet" href="login.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">

    <style>
        .message {
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .logo-img {
            height: 50px;
            width: auto;
        }
    </style>
</head>
<body>

<?php include_once "header.php"; ?><br>

<section class="login-container">
    <div class="login-box">
    <div class="logo-bloodplace">
            <a href="index.php" class="logo-link-bloodplace">
                <img src="images/logotipobpv.png" alt="Blood Place Voluntary" class="logo-img">
            </a>
        </div>
        <h2>Login</h2>

     
        <?php
        if (isset($_SESSION['error_message'])) {
            echo "<div class='message error'>" . $_SESSION['error_message'] . "</div>";
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo "<div class='message success'>" . $_SESSION['success_message'] . "</div>";
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Digite seu email" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>
        <p class="register-link">
            Não tem uma conta? <a href="register.php">Registre-se</a>
        </p>

        <?php

        $host = 'localhost';
        $dbname = 'blood_place';
        $user = 'root';
        $password = 'root';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = trim($_POST['email']);
                $password = $_POST['password'];

                if (empty($email) || empty($password)) {
                    $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
                    header("Location: login.php");
                    exit;
                }

                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['success_message'] = "Login realizado com sucesso! Bem-vindo, " . $user['name'] . ".";
                        header("Location: index.php");
                        exit;
                    } else {
                        $_SESSION['error_message'] = "Senha incorreta.";
                        header("Location: login.php");
                        exit;
                    }
                } else {
                    $_SESSION['error_message'] = "Usuário não encontrado.";
                    header("Location: login.php");
                    exit;
                }
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erro ao conectar ao banco de dados: " . $e->getMessage();
            header("Location: login.php");
            exit;
        }
        ?>
    </div>
</section>

<?php include_once "footer.php"; ?>

</body>
</html>

<?php

ob_end_flush();
?>
