<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', 'root', 'blood_place');
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT name, email, blood_type, birthdate, city FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


$perfil_completo = !empty($user['birthdate']) && !empty($user['blood_type']) && !empty($user['city']);


function verificarEstoque($tipo_sanguineo, $conn)
{

    $sql = "SELECT COUNT(*) as total 
            FROM doacoes d
            JOIN campanhas c ON d.campanha_id = c.id
            WHERE c.tipo_sanguineo = ? AND d.status = 'estoque'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tipo_sanguineo);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['total'] > 0;
}

$estoque_disponivel = verificarEstoque($user['blood_type'], $conn);
if (!$estoque_disponivel) {
    $mensagem_estoque = "<div class='error'>Não há estoque disponível para o seu tipo sanguíneo ({$user['blood_type']}).</div>";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $tipo_sangue = $_POST['tipo_sangue'];
    $tipo_doacao = $_POST['tipo_doacao'];
    $observacoes = trim($_POST['observacoes']);
    $setor = trim($_POST['setor']);
    $patologia = trim($_POST['patologia']);

    if (verificarEstoque($tipo_sangue, $conn)) {
        $sql = "INSERT INTO reservas (nome, email, telefone, data, horario, tipo_sangue, tipo_doacao, observacoes, setor,patologia,created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss",$nome, $email, $telefone, $data, $horario, $tipo_sangue, $tipo_doacao, $observacoes,$setor,$patologia);

        if ($stmt->execute()) {
            $mensagem = "<div class='success'>Agendamento realizado com sucesso!</div>";
        } else {
            $mensagem = "<div class='error'>Erro ao agendar: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $mensagem = "<div class='error'>Não há estoque disponível para o tipo sanguíneo {$tipo_sangue}.</div>";
    }
}

$sql = "SELECT id, data, horario, tipo_sangue, tipo_doacao, patologia, setor, status FROM reservas WHERE email = ? ORDER BY data DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$reservas = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Sangue - Blood Place Voluntary</title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .schedule-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #d64541;
        }

        form {
            display: grid;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            color: #333;
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }

        button {
            background-color: #d64541;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b93834;
        }

        #mensagem {
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
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

        .history-container {
            margin-top: 30px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .history-table th,
        .history-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .history-table th {
            background-color: #d64541;
            color: white;
        }

        .disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        input:focus {

            outline: none;
            border: 2px solid #D11A1A;
        }

        textarea:focus,
        select:focus {
            outline: none;
            border: 2px solid #D11A1A;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            width: 85%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            border: 2px solid #D11A1A;
        }

        .modal-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .modal-header h3 {
            color: #D11A1A;
            font-size: 1.8em;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .modal-header h3 i {
            font-size: 1.4em;
        }

        .modal-content ul {
            list-style: none;
            padding: 0;
            margin: 0 0 25px 0;
            text-align: left;
        }

        .modal-content li {
            padding: 12px 20px;
            margin: 8px 0;
            background: #fff5f5;
            border-left: 4px solid #D11A1A;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s ease;
        }

        .modal-content li:hover {
            transform: translateX(10px);
        }

        .modal-content li i {
            color: #D11A1A;
            min-width: 25px;
        }

        .btn-modal {
            background: #D11A1A;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-modal:hover {
            background: #b31515;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(209, 26, 26, 0.3);
        }

        .close {
            position: absolute;
            right: 25px;
            top: 20px;
            color: #aaa;
            font-size: 32px;
            font-weight: 300;
            transition: all 0.3s ease;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #D11A1A;
            transform: rotate(90deg);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @media (max-width: 600px) {
            .modal-content {
                width: 90%;
                padding: 20px;
            }

            .modal-header h3 {
                font-size: 1.4em;
            }
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .history-container {
            overflow-x: auto;
            margin-top: 30px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            min-width: 600px;
        }

        .history-table th,
        .history-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .history-table th {
            background-color: #d64541;
            color: white;
        }

        @media (max-width: 600px) {
            .history-table {
                font-size: 14px;
            }

            .history-table th,
            .history-table td {
                padding: 6px;
            }
        }

        .status-completado {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .btn-pdf {
    background-color: #d64541;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
    margin-top: 5px;
}

.btn-pdf:hover {
    background-color: #b93834;
}
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <br>
    <?php if (!$perfil_completo): ?>
        <div id="profileWarningModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div class="modal-header">
                    <h3>
                        <i class="fas fa-exclamation-triangle"></i>
                        Perfil Incompleto!
                    </h3>
                    <p>Para fazer reservas, complete seu cadastro:</p>
                </div>
                <ul>
                    <?php if (empty($user['birthdate'])): ?>
                        <li>
                            <i class="fas fa-birthday-cake"></i>
                            Data de Nascimento
                        </li>
                    <?php endif; ?>
                    <?php if (empty($user['blood_type'])): ?>
                        <li>
                            <i class="fas fa-tint"></i>
                            Tipo Sanguíneo
                        </li>
                    <?php endif; ?>
                    <?php if (empty($user['city'])): ?>
                        <li>
                            <i class="fas fa-city"></i>
                            Cidade
                        </li>
                    <?php endif; ?>
                </ul>
                <a href="edit_profile.php" class="btn-modal">
                    <i class="fas fa-user-edit"></i>
                    Completar Perfil
                </a>
            </div>
        </div>
    <?php endif; ?>
    <div class="schedule-container">
        <h1><i class="fas fa-tint"></i> Reservar Sangue</h1>
        <?php if (isset($mensagem_estoque)) : ?>
            <div id="mensagem" class="error">
                <?php echo $mensagem_estoque; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($mensagem)) : ?>
            <div id="mensagem" class="<?php echo strpos($mensagem, 'Erro') !== false ? 'error' : 'success'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form id="form-reserva" action="" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" required>
            </div>
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required>
            </div>
            <div class="form-group">
                <label for="patologia">Patologia (se aplicável):</label>
                <input type="text" id="patologia" name="patologia" placeholder="Digite alguma patologia existente (opcional)">
            </div>

            <div class="form-group">
                <label for="setor">Setor Hospitalar:</label>
                <input type="text" id="setor" name="setor" required placeholder="Ex: Hemocentro Central, Ala Norte">
            </div>
            <div class="form-group">
                <label for="horario">Horário:</label>
                <select id="horario" name="horario" required>
                    <option value="08:00">08:00</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_sangue">Tipo Sanguíneo:</label>
                <select id="tipo_sangue" name="tipo_sangue" required <?php if (!$estoque_disponivel) echo 'disabled'; ?>>
                    <option value="">Selecione...</option>
                    <option value="A+" <?php if ($user['blood_type'] == 'A+') echo 'selected'; ?>>A+</option>
                    <option value="A-" <?php if ($user['blood_type'] == 'A-') echo 'selected'; ?>>A-</option>
                    <option value="B+" <?php if ($user['blood_type'] == 'B+') echo 'selected'; ?>>B+</option>
                    <option value="B-" <?php if ($user['blood_type'] == 'B-') echo 'selected'; ?>>B-</option>
                    <option value="AB+" <?php if ($user['blood_type'] == 'AB+') echo 'selected'; ?>>AB+</option>
                    <option value="AB-" <?php if ($user['blood_type'] == 'AB-') echo 'selected'; ?>>AB-</option>
                    <option value="O+" <?php if ($user['blood_type'] == 'O+') echo 'selected'; ?>>O+</option>
                    <option value="O-" <?php if ($user['blood_type'] == 'O-') echo 'selected'; ?>>O-</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_doacao">Tipo de Doação:</label>
                <select id="tipo_doacao" name="tipo_doacao" required>
                    <option value="Sangue Total">Sangue Total</option>
                    <option value="Plaquetas">Plaquetas</option>
                    <option value="Plasma">Plasma</option>
                </select>
            </div>
            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea id="observacoes" name="observacoes" rows="4" placeholder="Digite informações adicionais (opcional)"></textarea>
            </div>
            <button type="submit" <?php if (!$estoque_disponivel) echo 'disabled'; ?>>Agendar</button>
        </form>
        <div class="history-container">
            <h2>Histórico de Reservas</h2>
            <table class="history-table">
                <tr>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Tipo Sanguíneo</th>
                    <th>Tipo de Doação</th>
                    <th>Patologia</th>
                    <th>Setor</th>
                    <th>Status</th>
                </tr>
                <?php if ($reservas->num_rows > 0) { ?>
                    <?php while ($reserva = $reservas->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['data']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['horario']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['tipo_sangue']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['tipo_doacao']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['patologia'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($reserva['setor']); ?></td>
                            <td>
                                <span class="status-<?php echo htmlspecialchars($reserva['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($reserva['status'])); ?>
                                </span>
                                <?php if ($reserva['status'] === 'completado' || $reserva['status'] === 'aceito'): ?>
                                    <br>
                                    <a href="gerar_pdf.php?reserva_id=<?php echo $reserva['id']; ?>" class="btn-pdf">
                                        <i class="fas fa-file-pdf"></i> Gerar PDF
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7">Nenhuma reserva encontrada.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </div>


    <br><br><br><br><br>
    <?php include 'footer.php'; ?>
    <script>
        const modal = document.getElementById('profileWarningModal');
        if (modal) {
            const closeBtn = modal.querySelector('.close');

            closeBtn.onclick = () => modal.style.display = 'none';

            window.onclick = (event) => {
                if (event.target === modal) modal.style.display = 'none';
            }

            document.querySelector('form').addEventListener('submit', (e) => {
                <?php if (!$perfil_completo): ?>
                    e.preventDefault();
                    modal.style.display = 'block';
                <?php endif; ?>
            });
        }
    </script>
</body>

</html>