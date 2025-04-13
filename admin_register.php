<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=blood_place", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $admin_name = trim($_POST['name']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($password) && !empty($confirmPassword)) {
        if ($password === $confirmPassword) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE username = :username");
            $stmt->bindValue(':username', $username);
            $stmt->execute();

            if ($stmt->fetchColumn() == 0) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

               $insertStmt = $conn->prepare("INSERT INTO admins (username, password,admin_name) VALUES (:username, :password,:admin_name)");
                $insertStmt->bindValue(':username', $username);
                $insertStmt->bindValue(':password', $hashedPassword);
                $insertStmt->bindValue(':admin_name', $admin_name);

                if ($insertStmt->execute()) {
                    $_SESSION['message'] = "Administrador registrado com sucesso!";
                    header("Location: login.php");
                    exit;
                } else {
                    $error = "Erro ao registrar administrador.";
                }
            } else {
                $error = "Nome de usuário já existe.";
            }
        } else {
            $error = "As senhas não coincidem.";
        }
    } else {
        $error = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Administrador - BloodPlace</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;

            background: url('uploads/slider2.jpg') no-repeat center center/cover;
            background-color: rgba(255, 255, 255, 0.8);
            background-blend-mode: lighten;
            padding: 30px;


            overflow: hidden;
        }

        .register-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #27293d;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 14px;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #27293d;
            background: #fff;
        }

        .btn {
            background: #D11A1A;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #D11A1A;
        }

        .error,
        .success {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .logo-img {
            height: 100px;
            width: auto;
            margin-left: 33%;
        }
        .link-login{
            text-decoration: none;
            color:  #D11A1A;
            text-align: center;
            padding: 5px;
            margin: 5px;
            display: block;
        }
        .link-login:hover{
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-box">
        <div class="login-box">
            <div class="logo-bloodplace">
                <a href="index.php" class="logo-link-bloodplace">
                    <img src="images/logotipobpv.png" alt="Blood Place Voluntary" class="logo-img">
                </a>
            </div>
            <h2>Registrar Administrador</h2>
            <form method="POST" action="admin_register.php">
            <div class="form-group">
                    <label for="name">Nome Completo do Administrador</label>
                    <input type="text" id="name" name="name" placeholder="Informe o nome" required>
                </div>
                <div class="form-group">
                    <label for="username">Nome de Usuário</label>
                    <input type="text" id="username" name="username" placeholder="Digite seu usuário" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirme sua senha" required>
                </div>
                <button type="submit" class="btn">Registrar</button>
                <a class="link-login" href="admin_login.php">Iniciar Sessão</a>
                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php elseif (!empty($_SESSION['message'])): ?>
                    <p class="success"><?php echo $_SESSION['message'];
                                        unset($_SESSION['message']); ?></p>
                <?php endif; ?>
            </form>
        </div>
</body>

</html>