<?php
include_once 'configu.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : 'uploads/profile_pictures/default-profile.jpeg';
} else {

    $_SESSION['profile_picture'] = 'uploads/profile_pictures/default-profile.jpeg';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $mensagem = trim($_POST["mensagem"]);

    if (empty($nome) || empty($mensagem)) {
        $message = "Por favor, preencha todos os campos.";
        $messageClass = "error";
    } else {
        try {
            $sql = "INSERT INTO depoimentos (nome, mensagem, data) VALUES (:nome, :mensagem, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':mensagem', $mensagem);

            if ($stmt->execute()) {
                $message = "Depoimento enviado com sucesso!";
                $messageClass = "success";
            } else {
                $message = "Erro ao enviar o depoimento.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "Erro ao processar a solicitação: " . $e->getMessage();
            $messageClass = "error";
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT nome, mensagem, data FROM depoimentos ORDER BY data DESC");
    $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depoimentos - Blood Place Voluntary</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Aptos', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .testimonials-section {
            background-color: #ffffff;
            padding: 50px 20px;
            margin: 40px;
            border-radius: 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .testimonials-section h2 {
            font-size: 36px;
            color: #D11A1A;
            text-align: center;
            margin-bottom: 40px;
        }

        .testimonial {
            background-color: #fff5f5;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            transition: transform 0.2s ease;
        }

        .testimonial:hover {
            transform: translateY(-5px);
        }

        .testimonial p {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }

        .testimonial .author {
            font-weight: bold;
            color: #D11A1A;
        }

        .testimonial .date {
            font-size: 14px;
            color: #777;
        }

        .testimonial-form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 40px;
        }

        .testimonial-form h2 {
            color: #D11A1A;
            text-align: center;
            margin-bottom: 20px;
        }

        .testimonial-form label {
            display: block;
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .testimonial-form input[type="text"],
        .testimonial-form textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .testimonial-form button {
            background-color: #D11A1A;
            color: #ffffff;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .testimonial-form button:hover {
            background-color: #B01717;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin: 20px auto;
            width: 80%;
            text-align: center;
            font-size: 16px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .testimonials-section,
            .testimonial-form {
                margin: 20px;
                padding: 20px;
            }

            .testimonials-section h2,
            .testimonial-form h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <?php include_once "header.php"; ?>

    <main><br>
        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <section class="testimonials-section">
            <h2><i class="fas fa-comments"></i> Depoimentos de Nossos Doadores</h2>

            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial">
                        <p class="author">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($testimonial['nome'], ENT_QUOTES); ?>
                        </p>
                        <p><?php echo htmlspecialchars($testimonial['mensagem'], ENT_QUOTES); ?></p>
                        <p class="date">
                            <i class="fas fa-calendar-alt"></i> <?php echo date("d/m/Y", strtotime($testimonial['data'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum depoimento encontrado.</p>
            <?php endif; ?>
        </section>

        <section class="testimonial-form">
            <h2>Deixe seu Depoimento</h2>
            <form action="" method="post">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" placeholder="Seu nome" required><br>

                <label for="mensagem">Depoimento:</label>
                <textarea name="mensagem" id="mensagem" placeholder="Escreva seu depoimento aqui..." required></textarea><br>

                <button type="submit">Enviar Depoimento</button>
            </form>
        </section>
    </main>

    <?php include_once "footer.php"; ?>
</body>

</html>