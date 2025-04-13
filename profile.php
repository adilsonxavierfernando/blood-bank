<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'blood_place';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $pdo->prepare("SELECT name, email, birthdate, blood_type, city, profile_picture, cover_picture FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Usuário não encontrado.";
        exit;
    }



    $stmt = $pdo->prepare("SELECT SUM(quantidade) AS total_ml_doado FROM doacoes WHERE doador_id = :id AND status IN ('estoque', 'doado')");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $total_ml_doado = $stmt->fetch(PDO::FETCH_ASSOC)['total_ml_doado'] ?? 0;


    $stmt2 = $pdo->prepare("SELECT COUNT(*) AS total_doacoes FROM doacoes WHERE doador_id = :id AND status IN ('estoque', 'doado')");
    $stmt2->bindParam(':id', $_SESSION['user_id']);
    $stmt2->execute();
    $num_doacoes = $stmt2->fetch(PDO::FETCH_ASSOC)['total_doacoes'] ?? 0;



    $stmt = $pdo->prepare("SELECT data, quantidade FROM doacoes WHERE doador_id = :id AND status = 'estoque' ORDER BY data DESC");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $historico_doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE cidade = :cidade ORDER BY data_inicio DESC LIMIT 3");
    $stmt->bindParam(':cidade', $user['city']);
    $stmt->execute();
    $campanhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT data FROM doacoes WHERE doador_id = :id AND status = 'estoque' ORDER BY data DESC LIMIT 1");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $ultima_doacao = $stmt->fetch(PDO::FETCH_ASSOC)['data'] ?? "Nenhuma doação registrada.";
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Blood Place Voluntary</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        .profile-container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        .cover-photo {
            width: 100%;
            height: 250px;
            background-size: cover;
            background-position: center;
            border-radius: 10px 10px 0 0;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            margin: -75px auto 15px;
            border: 4px solid white;
        }

        .profile-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .profile-info {
            padding: 10px;
        }

        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-actions a {
            background-color: #D11A1A;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .profile-actions a:hover {
            background-color: #b31212;
        }

        .profile-section {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge {
            background-color: #ffc107;
            color: black;
            padding: 8px 12px;
            border-radius: 5px;
        }




        @media (max-width: 768px) {
            .profile-summary {
                grid-template-columns: 1fr;
            }

            .profile-photo {
                width: 120px;
                height: 120px;
            }

            table {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include_once "header.php" ?><br>

    <section class="profile-container">

        <h2>Bem-vindo, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <div class="profile-summary">
            <div class="profile-info">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Data de Nascimento:</strong> <?php echo date("d/m/Y", strtotime($user['birthdate'])); ?></p>
                <p><strong>Tipo Sanguíneo:</strong> <?php echo htmlspecialchars($user['blood_type']); ?></p>
                <p><strong>Cidade:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
                <p><strong>Total de Doações:</strong> <?php echo number_format($num_doacoes); ?> doações</p>
                <p><strong>Última Doação:</strong> <?php echo $ultima_doacao; ?></p>
            </div>
            <div class="profile-actions">
                <a href="edit_profile.php" class="btn">Editar Perfil</a>
                <a href="schedule_donation.php" class="btn">Agendar Doação</a>
                <!---  <a href="share_profile.php" class="btn">Compartilhar Perfil</a>-->
            </div>
        </div>


        <div class="profile-section">
            <h3>Histórico de Doações</h3>
            <canvas id="doacoesChart"></canvas>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Quantidade (ml)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($historico_doacoes) > 0): ?>
                        <?php foreach ($historico_doacoes as $doacao): ?>
                            <tr>
                                <td><?php echo date("d/m/Y", strtotime($doacao['data'])); ?></td>
                                <td><?php echo $doacao['quantidade']; ?> ml</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Você ainda não fez nenhuma doação aprovada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <style>
            .profile-section {
                background: #fff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin: 20px 0;
                font-family: Arial, sans-serif;
            }

            .profile-section h3 {
                color: #d32f2f;
                font-size: 22px;
                margin-bottom: 15px;
                text-transform: uppercase;
                border-bottom: 2px solid #d32f2f;
                padding-bottom: 5px;
            }

            .profile-section ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .profile-section li {
                background: #f9f9f9;
                padding: 12px;
                margin-bottom: 8px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                font-size: 16px;
                border-left: 4px solid #d32f2f;
            }

            .profile-section li strong {
                color: #333;
            }

            .profile-section li::before {
                content: "📅";
                margin-right: 10px;
                font-size: 18px;
            }

            .profile-section li:last-child {
                margin-bottom: 0;
            }

            .profile-section .no-campaign {
                color: #777;
                font-style: italic;
                text-align: center;
                padding: 10px;
            }
        </style>

        <div class="profile-section">
            <h3>📢 Próximas Campanhas</h3>
            <ul>
                <?php if (count($campanhas) > 0): ?>
                    <?php foreach ($campanhas as $campanha): ?>
                        <li>
                            <strong>Data:</strong> <?php echo date("d/m/Y", strtotime($campanha['data_inicio'])); ?> -
                            <strong>Local:</strong> <?php echo htmlspecialchars($campanha['local']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no-campaign">🚫 Não há campanhas de doação agendadas na sua cidade.</li>
                <?php endif; ?>
            </ul>
        </div>



        <div class="profile-section">
            <h3>Conquistas</h3>
            <div class="badges">
                <?php if ($num_doacoes >= 10): ?>
                    <div class="badge">🏆 10 Doações</div>
                <?php endif; ?>
                <?php if ($num_doacoes >= 5): ?>
                    <div class="badge">🎖️ 5 Doações</div>
                <?php endif; ?>
                <?php if ($num_doacoes >= 1): ?>
                    <div class="badge">🥇 1ª Doação</div>
                <?php endif; ?>
            </div>
        </div>



    </section><br><br>

    <?php include_once "footer.php" ?>

    <script>
        const ctx = document.getElementById('doacoesChart').getContext('2d');
        const doacoesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($historico_doacoes, 'data')); ?>,
                datasets: [{
                    label: 'Quantidade (ml)',
                    data: <?php echo json_encode(array_column($historico_doacoes, 'quantidade')); ?>,
                    backgroundColor: '#D11A1A',
                    borderColor: '#9e0c0c',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>