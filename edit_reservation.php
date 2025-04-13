<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$host = 'localhost';
$dbname = 'blood_place';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

if (!isset($_GET['id'])) {
    die("ID da reserva não especificado.");
}

$id = $_GET['id'];
$query = "SELECT * FROM reservas WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Reserva não encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $tipo_sangue = $_POST['tipo_sangue'];
    $tipo_doacao = $_POST['tipo_doacao'];
    $observacoes = $_POST['observacoes'];

    $updateQuery = "UPDATE reservas SET nome = :nome, email = :email, telefone = :telefone, data = :data, horario = :horario, tipo_sangue = :tipo_sangue, tipo_doacao = :tipo_doacao, observacoes = :observacoes WHERE id = :id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':horario', $horario);
    $stmt->bindParam(':tipo_sangue', $tipo_sangue);
    $stmt->bindParam(':tipo_doacao', $tipo_doacao);
    $stmt->bindParam(':observacoes', $observacoes);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Reserva atualizada com sucesso!";
        header("Location: view_reservations.php");
        exit;


        exit;
    } else {
        echo "Erro ao atualizar a reserva.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f0f0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .admin-dashboard {
            display: grid;
            grid-template-columns: 250px auto;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #D11A1A;
            padding: 20px;
            color: #fff;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .menu {
            list-style: none;
        }

        .menu li {
            margin: 15px 0;
        }

        .menu a {
            color: #b0b0b0;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .menu a:hover {
            background-color:  #8f1a1a;
            color: white;
        }

        .content {
            background: url('uploads/slider2.jpg') no-repeat center center/cover;
            background-color: rgba(255, 255, 255, 0.8);
            background-blend-mode: lighten;
            padding: 30px;
        }

        h1 {
            text-align: center;
        }

        .content h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        button {
            background-color: #D11A1A;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 5px;
            margin-top: 15px;
        }

        button:hover {
            background-color:  #8f1a1a;
        }
    </style>
</head>

<body>
    <div class="admin-dashboard">
    <aside class="sidebar">
            <h2>Painel Admin</h2>
            <ul class="menu">
                <li><a href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a href="add_campaign.php"><i class="bi bi-plus-circle"></i> Adicionar Campanha</a></li>
                <li><a href="view_campaigns.php"><i class="bi bi-list"></i> Ver Campanhas</a></li>
                <li><a href="view_donors.php"><i class="bi bi-people"></i> Doadores</a></li>
                 <li><a href="view_donations.php"><i class="bi bi-droplet"></i> Doações</a></li>
                <li><a href="view_stock_donations.php"><i class="bi bi-box"></i> Estoque</a></li>
                <li><a href="view_message.php"><i class="bi bi-envelope-fill"></i> Mensagens</a></li>
                <li><a href="view_reservations.php"><i class="bi bi-calendar-check"></i> Reservas</a></li>
                <li><a href="add_slider.php"><i class="bi bi-images"></i> Adicionar Slide</a></li>
                <li><a href="manage_sliders.php"><i class="bi bi-sliders"></i> Gerenciar Slides</a></li>
                <li><a href="admin_logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
            </ul>

        </aside>

        <main class="content">
            <h1>Editar Reserva</h1>
            <div class="container">
                <form method="POST">
                    <label>Nome:</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($reservation['nome']); ?>" required>

                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($reservation['email']); ?>" required>

                    <label>Telefone:</label>
                    <input type="text" name="telefone" value="<?php echo htmlspecialchars($reservation['telefone']); ?>" required>

                    <label>Data:</label>
                    <input type="date" name="data" value="<?php echo htmlspecialchars($reservation['data']); ?>" required>

                    <label>Horário:</label>
                    <input type="time" name="horario" value="<?php echo htmlspecialchars($reservation['horario']); ?>" required>

                    <label>Tipo de Sangue:</label>
                    <input type="text" name="tipo_sangue" value="<?php echo htmlspecialchars($reservation['tipo_sangue']); ?>" required>

                    <label>Tipo de Doação:</label>
                    <input type="text" name="tipo_doacao" value="<?php echo htmlspecialchars($reservation['tipo_doacao']); ?>" required>

                    <label>Observações:</label>
                    <textarea name="observacoes" required><?php echo htmlspecialchars($reservation['observacoes']); ?></textarea>

                    <button type="submit">Atualizar</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>