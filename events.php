<?php
include_once "header.php";
include_once "config.php";


$query = "SELECT id, local, cidade, data_inicio, data_fim, tipo_sanguineo, descricao FROM campanhas ORDER BY data_inicio ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campanhas de Doação - Blood Place Voluntary</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .campaigns-header-unique {
            text-align: center;
            background: linear-gradient(to right, rgba(209, 26, 26, 0.9), rgba(50, 50, 50, 0.7));
            color: white;
            padding: 50px;
        }


        .campaign-card-unique {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .campaign-card-unique h2 {
            color: #d11a1a;
        }

        .campaign-card-unique p {
            margin: 8px 0;
        }
    </style>
</head>

<body class="campaigns-page">

<br><br>
    <header class="campaigns-header-unique">
        <h1><i class="bi bi-calendar-event"></i> Campanhas de Doação de Sangue</h1>
        <p>Confira as próximas campanhas de doação em sua região</p>
    </header>

    <main class="campaigns-container">
        <?php if ($result && $result->rowCount() > 0): ?>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="campaign-card-unique">
                    <h2>Local: <?php echo htmlspecialchars($row['local']); ?></h2>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($row['cidade'] ?? 'Não especificada'); ?></p>
                    <p><strong>Data:</strong> <?php echo date("d/m/Y", strtotime($row['data_inicio'])); ?> a <?php echo date("d/m/Y", strtotime($row['data_fim'])); ?></p>
                    <p><strong>Tipo Sanguíneo:</strong> <?php echo htmlspecialchars($row['tipo_sanguineo']); ?></p>
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($row['descricao']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Não há campanhas de doação de sangue agendadas no momento.</p>
        <?php endif; ?>
    </main>
    <br><br><br> <br><br><br>
    <br><br><br> <br><br><br>
    <?php include_once "footer.php"; ?>
</body>

</html>

<?php

$conn = null;
?>