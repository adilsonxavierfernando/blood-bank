<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: admin_login.php");
  exit;
}

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $local = trim($_POST['local'] ?? '');
  $cidade = trim($_POST['cidade'] ?? '');
  $data_inicio = $_POST['data_inicio'] ?? '';
  $data_fim = $_POST['data_fim'] ?? '';
  $tipo_sanguineo = trim($_POST['tipo_sanguineo'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');

  if (!empty($local) && !empty($cidade) && !empty($data_inicio) && !empty($data_fim) && !empty($tipo_sanguineo) && !empty($descricao)) {
    try {
      $sql = "INSERT INTO campanhas (local, cidade, data_inicio, data_fim, tipo_sanguineo, descricao) 
                    VALUES (:local, :cidade, :data_inicio, :data_fim, :tipo_sanguineo, :descricao)";

      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':local', $local);
      $stmt->bindParam(':cidade', $cidade);
      $stmt->bindParam(':data_inicio', $data_inicio);
      $stmt->bindParam(':data_fim', $data_fim);
      $stmt->bindParam(':tipo_sanguineo', $tipo_sanguineo);
      $stmt->bindParam(':descricao', $descricao);

      if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Campanha adicionada com sucesso!";
        $_SESSION['tipo_mensagem'] = "sucesso";
      } else {
        $_SESSION['mensagem'] = "Erro ao adicionar campanha!";
        $_SESSION['tipo_mensagem'] = "erro";
      }
    } catch (PDOException $e) {
      $_SESSION['mensagem'] = "Erro ao executar a consulta: " . $e->getMessage();
      $_SESSION['tipo_mensagem'] = "erro";
    }
  } else {
    $_SESSION['mensagem'] = "Todos os campos são obrigatórios!";
    $_SESSION['tipo_mensagem'] = "erro";
  }

  header("Location: view_campaigns.php");
  exit();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel do Administrador - Blood Place Voluntary</title>
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

    h1 {
      text-align: center;
      margin-bottom: 20px;
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
      background-color: #8f1a1a;
      color: white;
    }

    .menu a i {
      margin-right: 10px;
    }

    .content {
      background: url('uploads/slider2.jpg') no-repeat center center/cover;
      background-color: rgba(255, 255, 255, 0.8);
      background-blend-mode: lighten;
      padding: 30px;

    }

    .content h1 {
      font-size: 2rem;
      margin-bottom: 20px;
    }

    form {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      margin: auto;
    }

    label {
      font-weight: 600;
      margin-top: 10px;
      display: block;
    }

    input,
    textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    button {
      background-color: #D11A1A;
      color: #fff;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      font-size: 1rem;
      border-radius: 5px;
    }

    button:hover {
      background-color: #8f1a1a;
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
        <li><a href="view_donations.php"><i class="bi bi-droplet"></i> Doações Agendadas</a></li>
        <li><a href="view_stock_donations.php"><i class="bi bi-box"></i> Estoque</a></li>
        <li><a href="view_message.php"><i class="bi bi-envelope-fill"></i> Mensagens</a></li>
        <li><a href="view_reservations.php"><i class="bi bi-calendar-check"></i> Reservas</a></li>
        <li><a href="add_slider.php"><i class="bi bi-images"></i> Adicionar Slide</a></li>
        <li><a href="manage_sliders.php"><i class="bi bi-sliders"></i> Gerenciar Slides</a></li>
        <li><a href="admin_logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
      </ul>

    </aside>



    <main class="content">
      <h1>Adicionar Nova Campanha</h1>
      <form action="add_campaign.php" method="POST">
        <label for="local">Local:</label>
        <input type="text" name="local" id="local" required>

        <label for="cidade">Cidade:</label>
        <input type="text" name="cidade" id="cidade" required>

        <label for="data_inicio">Data de Início:</label>
        <input type="date" name="data_inicio" id="data_inicio" required>

        <label for="data_fim">Data de Término:</label>
        <input type="date" name="data_fim" id="data_fim" required>

        <label for="tipo_sanguineo">Tipo Sanguíneo Necessário:</label>
        <input type="text" name="tipo_sanguineo" id="tipo_sanguineo" required>

        <label for="descricao">Descrição:</label>
        <textarea name="descricao" id="descricao" rows="5" required></textarea>

        <button type="submit">Adicionar Campanha</button>
      </form>
    </main>
  </div>
</body>

</html>