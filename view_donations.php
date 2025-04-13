<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: admin_login.php");
  exit;
}

require 'configu.php';

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doações Agendadas</title>
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
      text-align: center;
    }

    .menu {
      list-style: none;
      margin-top: 20px;
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
      margin-bottom: 20px;
    }

    .styled-table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }

    .styled-table th,
    .styled-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .styled-table th {
      background-color: #D11A1A;
      color: white;
    }

    .styled-table tr:hover {
      background-color: #f5f5f5;
    }

    .btn-action {
      padding: 8px 12px;
      background: #D11A1A;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-action:hover {
      background: #8f1a1a;
    }

    .message-container {
      max-width: 600px;
      margin: 0 auto 20px auto;
      padding: 12px;
      border-radius: 8px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: opacity 0.5s ease-in-out;
    }

    .message-container.success {
      background-color: #d4edda;
      color: #155724;
      border-left: 5px solid #28a745;
    }

    .message-container.error {
      background-color: #f8d7da;
      color: #721c24;
      border-left: 5px solid #dc3545;
    }

    .message-container i {
      margin-right: 10px;
    }
    .search-container {
  margin: 15px 0;
}

#searchInput {
  padding: 10px;
  border: 2px solid #D11A1A;
  border-radius: 25px;
  width: 100%;
  max-width: 400px;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s ease;
}

#searchInput:focus {
  outline: none;
  border-color: #8f1a1a;
  box-shadow: 0 0 8px rgba(209, 26, 26, 0.3);
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
      <?php if ($success_message): ?>
        <div class="message-container success">
          <i class="bi bi-check-circle-fill"></i> <?= $success_message ?>
          <span class="close-btn" onclick="closeMessage(this)">×</span>
        </div>
      <?php endif; ?>

      <?php if ($error_message): ?>
        <div class="message-container error">
          <i class="bi bi-exclamation-triangle-fill"></i> <?= $error_message ?>
          <span class="close-btn" onclick="closeMessage(this)">×</span>
        </div>
      <?php endif; ?>
      <h1>Doações Agendadas</h1>
      <div class="search-container" style="margin-bottom: 20px; text-align: center;">
  <input type="text" id="searchInput" placeholder="Pesquisar por código do doador..." 
         style="padding: 8px; width: 300px; border-radius: 5px; border: 1px solid #ddd;">
</div>
      <table class="styled-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Código do Doador</th>
            <th>ID da Campanha</th>
            <th>Quantidade</th>
            <th>Data Agendada</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT * FROM doacoes WHERE status = 'pendente'";

          $result = $pdo->query($query);
          if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr>";
              echo "<td>{$row['id']}</td>";
              echo "<td>HGM000{$row['doador_id']}-2025</td>";
              echo "<td>{$row['campanha_id']}</td>";
              echo "<td>{$row['quantidade']}</td>";
              echo "<td>{$row['data_agendada']}</td>";
              echo "<td><button class='btn-action' onclick=\"location.href='add_to_stock.php?id={$row['id']}'\">Adicionar ao Estoque</button></td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='6' style='text-align:center;'>Nenhuma doação agendada.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </main>
  </div>
  <script>
  document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('.styled-table tbody tr');
    
    let hasVisibleRows = false;
    
    tableRows.forEach(row => {
      const donorCode = row.cells[1].textContent.toLowerCase();
      if (donorCode.includes(searchTerm)) {
        row.style.display = '';
        hasVisibleRows = true;
      } else {
        row.style.display = 'none';
      }
    });

   
    const noResultsRow = document.querySelector('.no-results');
    if (!hasVisibleRows) {
      if (!noResultsRow) {
        const tbody = document.querySelector('.styled-table tbody');
        const tr = document.createElement('tr');
        tr.className = 'no-results';
        tr.innerHTML = `<td colspan="6" style="text-align:center;">Nenhuma doação encontrada para o código pesquisado.</td>`;
        tbody.appendChild(tr);
      }
    } else if (noResultsRow) {
      noResultsRow.remove();
    }
  });
</script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      setTimeout(function() {
        let messages = document.querySelectorAll(".message-container");
        messages.forEach(msg => msg.style.opacity = "0");
      }, 3000);
    });

    function closeMessage(el) {
      el.parentElement.style.opacity = "0";
    }
  </script>

</body>

</html>