<?php include_once "header.php" ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Blood Place Voluntary</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="register.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f8fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }


        header,
        footer {
            background: #b30000;
            color: #ffffff;
            padding: 15px;
            text-align: center;
        }

        a {
            color: #b30000;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }


        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 40px;
            background-color: #f7f8fc;
        }

        .register-box {
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .register-box h2 {
            color: #b30000;
            margin-bottom: 20px;
        }


        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #b30000;
        }

        .btn {
            background: #b30000;
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background: #990000;
        }


        .login-link {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            color: #b30000;
            font-weight: bold;
        }
    </style>
</head>


<body>

    <br><br><br><br><br><br>

    <section class="register-container">
        <div class="register-box">
            <div class="logo-bloodplace">
                <a href="index.php" class="logo-link-bloodplace">
                    <img src="images/logotipobpv.png" alt="Blood Place Voluntary" class="logo-img">
                </a>
            </div>
            <h2>Registro</h2>
            <form action="process_register.php" method="POST">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" placeholder="Digite seu nome completo" required>
                </div>
                <div class="form-group">
                    <label for="birthdate">Data de Nascimento</label>
                    <?php
                        $currentDate = date('Y-m-d');
                        $minDate = date('Y-m-d', strtotime('-18 years', strtotime($currentDate)));
                    ?>
                    <input type="date" id="birthdate" name="birthdate" placeholder="Data de Nascimento" max="<?php echo $minDate; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu email" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" placeholder="Crie uma senha" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirme a Senha</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirme sua senha" required>
                </div>
                <button type="submit" class="btn">Registrar</button>
            </form>
            <p class="login-link">
                Já tem uma conta? <a href="login.php">Faça login</a>
            </p>
        </div>
    </section>
    <br><br><br><br><br><br>

    <?php include_once "footer.php" ?>
</body>

</html>