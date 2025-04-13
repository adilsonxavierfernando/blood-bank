<?php
session_start();
require 'configu.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();


        $required_fields = [
            'doacao_id' => FILTER_VALIDATE_INT,
            'quantidade_retirada' => FILTER_VALIDATE_INT,
            'tecnico' => FILTER_SANITIZE_STRING,
            'hospital' => FILTER_SANITIZE_STRING,
            'data_retirada' => FILTER_SANITIZE_STRING,
            'setor' => FILTER_SANITIZE_STRING,
            'paciente' => FILTER_SANITIZE_STRING
        ];

        $data = [];
        foreach ($required_fields as $field => $filter) {
            $value = filter_input(INPUT_POST, $field, $filter);
            if (empty($value)) {
                throw new Exception("Preencha todos os campos obrigatórios!");
            }
            $data[$field] = $value;
        }


        $stmt = $pdo->prepare("SELECT quantidade FROM doacoes WHERE id = ?");
        $stmt->execute([$data['doacao_id']]);
        $doacao = $stmt->fetch();

        if ($data['quantidade_retirada'] > $doacao['quantidade']) {
            throw new Exception("Quantidade solicitada excede o estoque disponível!");
        }


        $novo_estoque = $doacao['quantidade'] - $data['quantidade_retirada'];
        $stmt = $pdo->prepare("UPDATE doacoes SET quantidade = ? WHERE id = ?");
        $stmt->execute([$novo_estoque, $data['doacao_id']]);


        $stmt = $pdo->prepare("INSERT INTO retiradas 
            (doacao_id, quantidade_retirada, tecnico, hospital, setor, paciente, data_retirada, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $data['doacao_id'],
            $data['quantidade_retirada'],
            $data['tecnico'],
            $data['hospital'],
            $data['setor'],
            $data['paciente'],
            $data['data_retirada'],
            filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_STRING)
        ]);

        $pdo->commit();
        $_SESSION['mensagem'] = "Retirada registrada com sucesso!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
    }
    header("Location: view_stock_donations.php");
    exit;
}



$query_ret = "SELECT r.*, u.name AS doador_name 
              FROM retiradas r
              JOIN doacoes d ON r.doacao_id = d.id
              JOIN users u ON d.doador_id = u.id
              ORDER BY r.data_retirada DESC";
$history = $pdo->query($query_ret)->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT d.*, u.name AS doador_name, c.local AS campanha_local
          FROM doacoes d
          JOIN users u ON d.doador_id = u.id
          JOIN campanhas c ON d.campanha_id = c.id";
$donations = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);


$bloodTypeData = $pdo->query(
    "SELECT u.blood_type, SUM(d.quantidade) as total 
     FROM doacoes d
     JOIN users u ON d.doador_id = u.id
     GROUP BY u.blood_type"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$evolutionData = $pdo->query(
    "SELECT DATE(data) AS data_doacao, SUM(quantidade) AS total_quantidade
     FROM doacoes
     GROUP BY DATE(data)
     ORDER BY DATE(data)"
)->fetchAll(PDO::FETCH_ASSOC);
$bloodTypeQuery = "SELECT u.blood_type, SUM(d.quantidade) as total 
                   FROM doacoes d
                   JOIN users u ON d.doador_id = u.id
                   GROUP BY u.blood_type";
$bloodTypeResult = $pdo->query($bloodTypeQuery);
$bloodTypeData = $bloodTypeResult->fetchAll(PDO::FETCH_KEY_PAIR);


$query = "SELECT d.id, u.name AS doador_name, u.blood_type, c.local AS campanha_local, 
                 d.quantidade, d.data, d.status 
          FROM doacoes d
          JOIN users u ON d.doador_id = u.id
          JOIN campanhas c ON d.campanha_id = c.id";
$donations = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estoque.css">
</head>

<body>
    <div class="admin-dashboard">
        <?php include 'admin_sidebar.php'; ?>

        <main class="content container-fluid">
            <?php include 'alertas.php'; ?>


            <section class="charts-section mb-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-card card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Distribuição por Tipo Sanguíneo</h5>
                                <canvas id="bloodTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Evolução Temporal</h5>
                                <canvas id="timeSeriesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <section class="stock-table mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Registros de Estoque</h5>
                        <div>
                            <button class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#retiradaModal">
                                <i class="bi bi-box-arrow-right"></i> Nova Retirada
                            </button>

                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="bi bi-file-earmark-pdf"></i> Relatório
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Doador</th>
                                        <th>Tipo Sanguíneo</th>
                                        <th>Campanha</th>
                                        <th>Quantidade</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donations as $row): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['doador_name']) ?></td>
                                            <td><?= $row['blood_type'] ?></td>
                                            <td><?= htmlspecialchars($row['campanha_local']) ?></td>
                                            <td><?= $row['quantidade'] ?> ml</td>
                                            <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                                            <td>
                                                <form action="update_status.php" method="POST">
                                                    <input type="hidden" name="doacao_id" value="<?= $row['id'] ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="pendente" <?= $row['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                                        <option value="estoque" <?= $row['status'] == 'estoque' ? 'selected' : '' ?>>Estoque</option>
                                                        <option value="utilizado" <?= $row['status'] == 'utilizado' ? 'selected' : '' ?>>Utilizado</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="filterModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="gerar_estoque_pdf.php" target="_blank">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Filtrar Relatório</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Data Inicial</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Data Final</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Tipo Sanguíneo</label>
                                    <select name="blood_type" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O-">O-</option>
                                        <option value="O+">O+</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="pendente">Pendente</option>
                                        <option value="estoque">Estoque</option>
                                        <option value="utilizado">Utilizado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Gerar Relatório</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="retiradaModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Registro de Retirada</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Tipo Sanguíneo*</label>
                                        <select class="form-select" id="bloodTypeSelect" required>
                                            <option value="">Selecione...</option>
                                            <?php
                                            $tipos = $pdo->query("SELECT DISTINCT blood_type FROM users")->fetchAll(PDO::FETCH_COLUMN);
                                            foreach ($tipos as $tipo) {
                                                echo "<option value='$tipo'>$tipo</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Doação Disponível*</label>
                                        <select class="form-select" name="doacao_id" id="doacaoSelect" required>
                                            <option value="">Primeiro selecione o tipo sanguíneo</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Disponível (ml)</label>
                                        <input type="text" class="form-control" id="quantidadeDisponivel" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Quantidade (ml)*</label>
                                        <input type="number" name="quantidade_retirada" class="form-control" required min="1">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Paciente*</label>
                                        <input type="text" name="paciente" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Instituição*</label>
                                        <input type="text" name="hospital" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Setor*</label>
                                        <select class="form-select" name="setor" required>
                                            <option value="Emergência">Banco Urgência</option>
                                            <option value="UTI">UTI</option>
                                            <option value="Centro Cirúrgico">Centro Cirúrgico</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Técnico*</label>
                                        <input type="text" name="tecnico" class="form-control"
                                            value="<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>"
                                            readonly required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Data*</label>
                                        <input type="date" name="data_retirada" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>

                                    <div class="col-12">
                                        <label>Observações</label>
                                        <textarea name="observacoes" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="filterRetiradasModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="gerar_retiradas_pdf.php" target="_blank">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Filtrar Retiradas</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Data Inicial</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Data Final</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                    <label>Tipo Sanguíneo</label>
                                    <select name="blood_type" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Gerar PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <section class="retiradas-history mb-5">
                <div class="card">
                    <div class="card-header">
                        <h5>Histórico de Retiradas</h5>
                    </div>
                    <div class="card-header d-flex justify-content-between align-items-center">

                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#filterRetiradasModal">
                            <i class="bi bi-file-earmark-pdf"></i> Gerar Relatório
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Doador</th>
                                        <th>Quantidade</th>
                                        <th>Técnico</th>
                                        <th>Instituição</th>
                                        <th>Setor</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $retirada): ?>
                                        <tr>
                                            <td><?= $retirada['id'] ?></td>
                                            <td><?= htmlspecialchars($retirada['doador_name']) ?></td>
                                            <td><?= $retirada['quantidade_retirada'] ?> ml</td>
                                            <td><?= htmlspecialchars($retirada['tecnico']) ?></td>
                                            <td><?= htmlspecialchars($retirada['hospital']) ?></td>
                                            <td><?= htmlspecialchars($retirada['setor']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($retirada['data_retirada'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('bloodTypeChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($bloodTypeData)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($bloodTypeData)) ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            }
        });

        new Chart(document.getElementById('timeSeriesChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($evolutionData, 'data_doacao')) ?>,
                datasets: [{
                    label: 'Estoque (ml)',
                    data: <?= json_encode(array_column($evolutionData, 'total_quantidade')) ?>,
                    borderColor: '#FF5733',
                    fill: false
                }]
            }
        });


        document.getElementById('bloodTypeSelect').addEventListener('change', function() {
            const tipo = this.value;
            const selectDoacoes = document.getElementById('doacaoSelect');

            if (tipo) {
                fetch(`get_doacoes.php?tipo=${encodeURIComponent(tipo)}`)
                    .then(response => response.json())
                    .then(data => {
                        selectDoacoes.innerHTML = data.options;
                    });
            }
        });


        document.getElementById('doacaoSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('quantidadeDisponivel').value = selected.dataset.quantidade || '';
        });
    </script>
</body>

</html>