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
  <title>Lista de Doadores - Blood Place Voluntary</title>
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


    h1 {
      text-align: center;
      font-size: 2rem;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      padding: 12px;
      
      text-align: left;
    }

    th {
      background-color: #D11A1A;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
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

    p {
      margin-top: 20px;
      font-size: 1.1rem;
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
      <h1>Lista de Doadores</h1>
      <a href="gerar_doadores_pdf.php" target="_blank" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Baixar PDF
                </a>
      <?php
      require_once 'configu.php';

      try {
        $query = "
        SELECT d.id, d.nome, d.email, d.telefone, u.blood_type, 
               TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) AS idade
        FROM doadores d
        JOIN users u ON d.email = u.email
        ORDER BY d.data_doacao DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $doadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        die("Erro ao buscar doadores: " . $e->getMessage());
      }

      if (count($doadores) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Telefone</th>
              <th>Tipo Sanguíneo</th>
              <th>Idade</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($doadores as $doador): ?>
              <tr>
                <td><?php echo htmlspecialchars($doador['id']); ?></td>
                <td><?php echo htmlspecialchars($doador['nome']); ?></td>
                <td><?php echo htmlspecialchars($doador['email']); ?></td>
                <td><?php echo htmlspecialchars($doador['telefone']); ?></td>
                <td><?php echo htmlspecialchars($doador['blood_type']); ?></td>
                <td><?php echo $doador['idade']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Nenhum doador encontrado.</p>
      <?php endif; ?>
      
    </main>
  </div>
</body>

</html>