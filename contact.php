<?php
session_start();

$host = 'localhost';
$dbname = 'blood_place';
$username = 'root'; 
$password = 'root'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : 'uploads/profile_pictures/default-profile.jpeg';
} else {

    $_SESSION['profile_picture'] = 'uploads/profile_pictures/default-profile.jpeg';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['feedback'] = ['type' => 'error', 'message' => 'Por favor, preencha todos os campos.'];
        header("Location: contact.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['feedback'] = ['type' => 'error', 'message' => 'O email fornecido não é válido.'];
        header("Location: contact.php");
        exit;
    }

    try {
        $sql = "INSERT INTO contatos (nome, email, mensagem, data_envio) VALUES (:nome, :email, :mensagem, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mensagem', $message);

        if ($stmt->execute()) {
            $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Mensagem enviada com sucesso!'];
        } else {
            $_SESSION['feedback'] = ['type' => 'error', 'message' => 'Erro ao enviar a mensagem.'];
        }
    } catch (PDOException $e) {
        $_SESSION['feedback'] = ['type' => 'error', 'message' => 'Erro ao processar a solicitação: ' . $e->getMessage()];
    }

    header("Location: contact.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato | Blood Place</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .contact-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            margin-bottom: 30px;
        }
        .contact-section h2 {
            margin-bottom: 20px;
            color: #e63946;
        }
        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #f7f7f7;
        }
        button {
            background: #e63946;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #d32f2f;
        }
        .info-section {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }
        .info-section h2 {
            color: #e63946;
        }
        .info-item {
            margin-bottom: 12px;
            font-size: 1.1em;
        }
        .info-item strong {
            color: #333;
        }
        .info-item a {
            color: #e63946;
            text-decoration: none;
        }
        .info-item a:hover {
            text-decoration: underline;
        }
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 30px;
        }
        .feedback {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .feedback.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .feedback.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            header h1 {
                font-size: 2em;
            }
            nav ul {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<?php include_once "header.php"; ?>

<div class="container">
    <?php if (isset($_SESSION['feedback'])): ?>
        <div class="feedback <?= $_SESSION['feedback']['type']; ?>">
            <?= $_SESSION['feedback']['message']; ?>
        </div>
        <?php unset($_SESSION['feedback']); ?>
    <?php endif; ?>

    <section class="contact-section">
        <h2>Envie uma mensagem</h2>
        <form action="contact.php" method="POST">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required placeholder="Digite seu nome">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Digite seu email">

            <label for="message">Mensagem:</label>
            <textarea id="message" name="message" rows="5" required placeholder="Digite sua mensagem"></textarea>

            <button type="submit">Enviar</button>
        </form>
    </section>

    <section class="info-section">
        <h2>Informações de Contato</h2>
        <div class="info-item"><strong>Endereço:</strong> Rua dos Doadores, 003 - Luena</div>
        <div class="info-item"><strong>Telefone:</strong> +244 933 889 652</div>
        <div class="info-item"><strong>Email:</strong> contato@bloodplace.com</div>
        <div class="info-item"><strong>Redes Sociais:</strong> <a href="#">Facebook</a> | <a href="#">Instagram</a></div>
    </section>
</div>

<?php include_once "footer.php"; ?>

</body>
</html>