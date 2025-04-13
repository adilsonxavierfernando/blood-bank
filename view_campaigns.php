<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: admin_login.php");
  exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campanhas Ativas - Blood Place Voluntary</title>

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

    h1 {
      text-align: center;
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

    .mensagem {
      padding: 15px;
      margin: 15px 0;
      border-radius: 5px;
      font-size: 16px;
      text-align: center;
    }

    .sucesso {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .erro {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }


    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #f9f9f9;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #D11A1A;
      color: white;
    }

    button {
      background-color: #D11A1A;
      color: #fff;
      border: none;
      padding: 8px 16px;
      cursor: pointer;
      font-size: 0.9rem;
      border-radius: 5px;
    }

    button:hover {
      background-color: #8f1a1a;
      ;
    }

    .actions {
      display: flex;
      gap: 10px;
    }
    .btn {
      background-color: #D11A1A;
      color: white;
      padding: 10px 15px;
      text-decoration: none;
      border-radius: 5px;
      display: inline-block;
      margin-top: 20px;
    }

    .btn:hover {
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
                 <li><a href="view_donations.php"><i class="bi bi-droplet"></i>  Doações Agendadas</a></li>
                <li><a href="view_stock_donations.php"><i class="bi bi-box"></i> Estoque</a></li>
                <li><a href="view_message.php"><i class="bi bi-envelope-fill"></i> Mensagens</a></li>
                <li><a href="view_reservations.php"><i class="bi bi-calendar-check"></i> Reservas</a></li>
                <li><a href="add_slider.php"><i class="bi bi-images"></i> Adicionar Slide</a></li>
                <li><a href="manage_sliders.php"><i class="bi bi-sliders"></i> Gerenciar Slides</a></li>
                <li><a href="admin_logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
            </ul>

        </aside>



    <main class="content">
      <h1>Lista de Campanhas Ativas</h1>
      <a href="gerar_campanha_pdf.php" target="_blank" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Baixar PDF
                </a><br><br>
      <table>
        <?php

        if (isset($_SESSION['mensagem'])) {
          $tipo = $_SESSION['tipo_mensagem'] == "sucesso" ? "sucesso" : "erro";
          echo "<div class='mensagem $tipo'>" . $_SESSION['mensagem'] . "</div>";
          unset($_SESSION['mensagem']);
          unset($_SESSION['tipo_mensagem']);
        }
        ?>

        <thead>
          <tr>
            <th>ID</th>
            <th>Local</th>
            <th>Cidade</th>
            <th>Data de Início</th>
            <th>Data de Fim</th>
            <th>Tipo Sanguíneo</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $host = 'localhost';
          $dbname = 'blood_place';
          $user = 'root';
          $password = 'root';

          try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT * FROM campanhas");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr>";
              echo "<td>{$row['id']}</td>";
              echo "<td>{$row['local']}</td>";
              echo "<td>{$row['cidade']}</td>";
              echo "<td>{$row['data_inicio']}</td>";
              echo "<td>{$row['data_fim']}</td>";
              echo "<td>{$row['tipo_sanguineo']}</td>";
              echo "<td>{$row['descricao']}</td>";
              echo "<td class='actions'>
                          <button onclick=\"location.href='edit_campaign.php?id={$row['id']}'\">Editar</button>
                          <button onclick=\"location.href='delete_campaign.php?id={$row['id']}'\">Excluir</button>
                        </td>";
              echo "</tr>";
            }
          } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
          }
          ?>
        </tbody>
      </table>
    </main>
  </div>
</body>

</html>