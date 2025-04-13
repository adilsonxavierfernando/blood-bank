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

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Usuário ou senha incorretos.";
        }
    } else {
        $error = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Administrador - BloodPlace</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
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

        .login-box {
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
            border: 1px solid #27293d;
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
            background: #8f1a1a;
        }

        .error {
            color: red;
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }

        .login-box footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        .login-box footer a {
            color: #d32f2f;
            text-decoration: none;
        }

        .login-box footer a:hover {
            text-decoration: underline;
        }

        .logo-img {
            height: 100px;
            width: auto;
       margin-left: 33%;
        }
      
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo-bloodplace">
            <a href="index.php" class="logo-link-bloodplace">
                <img src="images/logotipobpv.png" alt="Blood Place Voluntary" class="logo-img">
            </a>
        </div>
        <h2>Login do Administrador</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" placeholder="Digite seu usuário" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <footer>
            <p>Problemas para acessar? <a href="#">Contate o suporte</a></p><br>
            <hr><br>
            <p>Criar uma <a href="admin_register.php">Conta de Administrador</a></p>
        </footer>
    </div>
</body>

</html>