
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


$filtro_nome = isset($_GET['filtro_nome']) ? trim($_GET['filtro_nome']) : '';


$query = "SELECT * FROM reservas";
if (!empty($filtro_nome)) {
  $query .= " WHERE nome LIKE :filtro_nome";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);

if (!empty($filtro_nome)) {
  $stmt->bindValue(':filtro_nome', '%' . $filtro_nome . '%', PDO::PARAM_STR);
}

$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualizar Reservas - Blood Place Voluntary</title>
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
    }

    .message {
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
      font-size: 1rem;
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

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .action-buttons {
      display: flex;
      gap: 10px;
    }

    .btn {
      padding: 6px 12px;
      text-decoration: none;
      color: white;
      border-radius: 4px;
    }

    .btn-edit {
      background-color: #D11A1A;
    }

    .btn-delete {
      background-color: #D11A1A;
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
    }

    .status-select {
      padding: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
      background-color: #fff;
      cursor: pointer;
    }

    .status-select:focus {
      outline: none;
      border-color: #D11A1A;
    }
 
  .filtro-container {
  margin-bottom: 20px;
  display: flex;
  gap: 10px;
  align-items: center;
  }
  .filtro-container input[type="text"] {
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ccc;
  width: 300px;
  }
  .filtro-container button {
  padding: 8px 16px;
  background-color: #D11A1A;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  }
  .filtro-container button:hover {
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
      <h1>Reservas de Doação</h1>
      <div class="filtro-container">
        <form method="GET" action="">
          <input type="text" name="filtro_nome" placeholder="Filtrar por nome" value="<?php echo htmlspecialchars($filtro_nome); ?>">
          <button type="submit">Filtrar</button>
        </form>
        <a href="gerar_reserva_pdf.php" class="btn btn-edit" style="background-color:#D11A1A; padding:10px; color:white; text-decoration:none; border-radius:5px;" target="_blank">
          <i class="bi bi-file-earmark-pdf"></i> Baixar PDF
        </a>
      </div>

      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message success">
          <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
      <?php endif; ?>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Data</th>
            <th>Horário</th>
            <th>Tipo de Sangue</th>
            <th>Tipo de Doação</th>
            <th>Observações</th>
            <th>Data de Criação</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($reservations) > 0): ?>
            <?php foreach ($reservations as $reservation): ?>
              <tr>
                <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                <td><?php echo htmlspecialchars($reservation['nome']); ?></td>
                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                <td><?php echo htmlspecialchars($reservation['telefone']); ?></td>
                <td><?php echo htmlspecialchars($reservation['data']); ?></td>
                <td><?php echo htmlspecialchars($reservation['horario']); ?></td>
                <td><?php echo htmlspecialchars($reservation['tipo_sangue']); ?></td>
                <td><?php echo htmlspecialchars($reservation['tipo_doacao']); ?></td>
                <td><?php echo htmlspecialchars($reservation['observacoes']); ?></td>
                <td><?php echo htmlspecialchars($reservation['created_at']); ?></td>
                <td>
                  <form action="update_reservation_status.php" method="POST" style="display:inline;">
                    <input type="hidden" name="reserva_id" value="<?php echo $reservation['id']; ?>">
                    <select name="status" class="status-select" onchange="this.form.submit()">
                      <option value="pendente" <?php echo $reservation['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                      <option value="aceito" <?php echo $reservation['status'] === 'aceito' ? 'selected' : ''; ?>>Aceito</option>
                      <option value="completado" <?php echo $reservation['status'] === 'completado' ? 'selected' : ''; ?>>Completado</option>
                      <option value="cancelado" <?php echo $reservation['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                  </form>
                </td>
                <td>
                  <div class="action-buttons">
                    <a class="btn btn-edit" href="edit_reservation.php?id=<?php echo $reservation['id']; ?>">Editar</a>
                    <a class="btn btn-delete" href="delete_reservation.php?id=<?php echo $reservation['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta reserva?');">Remover</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="12">Nenhuma reserva encontrada.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>

</html>