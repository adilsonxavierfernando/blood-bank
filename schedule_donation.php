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


    $stmt = $pdo->prepare("SELECT name, birthdate, blood_type, city, email FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Usuário não encontrado.";
        exit;
    }


    $data_nascimento_faltando = empty($user['birthdate']);
    $perfil_completo = !$data_nascimento_faltando && !empty($user['blood_type']) && !empty($user['city']);


    $idade = '';
    if (!$data_nascimento_faltando) {
        $birthDate = new DateTime($user['birthdate']);
        $today = new DateTime('today');
        $idade = $birthDate->diff($today)->y;

        if ($perfil_completo && ($idade < 18 || $idade > 69)) {
            $_SESSION['error_message'] = "Idade não permitida para doação (18-69 anos)";
            header("Location: schedule_donation.php");
            exit;
        }
    }


    $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE data_inicio >= CURDATE() ORDER BY data_inicio ASC");
    $stmt->execute();
    $campanhas = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("
        SELECT d.data_agendada, d.quantidade, c.local, c.data_inicio, d.status 
        FROM doacoes d 
        JOIN campanhas c ON d.campanha_id = c.id 
        WHERE d.doador_id = :doador_id 
        AND (d.status = 'estoque' OR d.status = 'doado')
        ORDER BY d.data_agendada DESC
    ");
    $stmt->bindParam(':doador_id', $_SESSION['user_id']);
    $stmt->execute();
    $historico_doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$perfil_completo) {
            $_SESSION['error_message'] = "Complete seu perfil antes de agendar doações!";
            header("Location: schedule_donation.php");
            exit;
        }


        $campanha_id = trim($_POST['campanha_id']);
        $quantidade = trim($_POST['quantidade']);
        $data_agendada = trim($_POST['data_agendada']);
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);

        $erros = [];
        if (empty($campanha_id) || empty($quantidade) || empty($data_agendada) || empty($nome) || empty($email)) {
            var_dump($campanha_id, $quantidade, $data_agendada, $nome, $email);
            $erros[] = "Por favor, preencha todos os campos obrigatórios."+$_POST['campanha_id'].$_POST['quantidade'].$_POST['data_agendada'].$_POST['nome'].$_POST['email'];
        }
        if (!is_numeric($quantidade) || $quantidade < 200 || $quantidade > 500) {
            $erros[] = "Quantidade deve ser entre 200 e 500 ml.";
        }
        if (strtotime($data_agendada) < strtotime(date('Y-m-d'))) {
            $erros[] = "A data da doação não pode ser no passado.";
        }

        if (empty($erros)) {
            try {
                $pdo->beginTransaction();


                $stmt = $pdo->prepare("SELECT data_agendada FROM doacoes WHERE doador_id = :doador_id ORDER BY data_agendada DESC LIMIT 1");
                $stmt->bindParam(':doador_id', $_SESSION['user_id']);
                $stmt->execute();
                $ultima_doacao = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($ultima_doacao) {
                    $data_ultima = new DateTime($ultima_doacao['data_agendada']);
                    $data_minima = (clone $data_ultima)->modify('+90 days');
                    $hoje = new DateTime();

                    if ($hoje < $data_minima) {
                        $dias_restantes = $hoje->diff($data_minima)->days;
                        $erros[] = "Você deve esperar mais $dias_restantes dias para doar novamente.";
                    }
                }

                if (empty($erros)) {

                    $stmt = $pdo->prepare("INSERT INTO doacoes (doador_id, campanha_id, quantidade, data_agendada) VALUES (:doador_id, :campanha_id, :quantidade, :data_agendada)");
                    $stmt->execute([
                        ':doador_id' => $_SESSION['user_id'],
                        ':campanha_id' => $campanha_id,
                        ':quantidade' => $quantidade,
                        ':data_agendada' => $data_agendada
                    ]);


                    $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
                    $stmt->execute([
                        ':email' => $email,
                        ':id' => $_SESSION['user_id']
                    ]);

                    $pdo->commit();
                    $_SESSION['success_message'] = "Doação agendada com sucesso!";


                    require('fpdf/fpdf.php');
                    require('phpqrcode/qrlib.php');

                    $stmt = $pdo->prepare("SELECT u.name, u.birthdate, u.blood_type, u.city, u.email, c.local 
                                   FROM users u 
                                   JOIN campanhas c ON u.id = :user_id 
                                   WHERE u.id = :user_id");
                    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        die("Usuário não encontrado.");
                    }

                    $data_agendada_formatada = date('d/m/Y', strtotime($data_agendada));
                    $local_campanha = $user['local'];
                    $codigo_doador = 'HGM000' . $_SESSION['user_id'] . '-' . date('Y');

                    class PDF extends FPDF
                    {
                        // Cabeçalho
                        function Header()
                        {
                            $this->Image('images/logotipobpv.png', 90, 10, 30);
                            $this->Ln(20);
                            $this->SetFont('Arial', 'B', 16);
                            $this->SetTextColor(30, 30, 30);
                            $this->Cell(0, 10, 'Hospital Geral do Moxico', 0, 1, 'C');
                            $this->SetFont('Arial', 'I', 12);
                            $this->SetTextColor(80, 80, 80);
                            $this->Cell(0, 8, mb_convert_encoding('Agendamento Electronico de Doações - Blood Place Voluntary', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
                            $this->Ln(5);
                            $this->SetLineWidth(0.5);
                            $this->SetDrawColor(178, 34, 34); 
                            $this->Line(15, $this->GetY(), 195, $this->GetY());
                            $this->Ln(8);
                        }
                    
                        // Rodapé
                        function Footer()
                        {
                            $this->SetY(-20);
                            $this->SetFont('Arial', 'I', 8);
                            $this->SetTextColor(100, 100, 100);
                            $this->Cell(0, 10, mb_convert_encoding('Gerado em: ' . date('d/m/Y H:i'), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
                            $this->Cell(0, 10, mb_convert_encoding('Página ' . $this->PageNo(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
                        }
                    }
                    
                    // Criando o PDF
                    $pdf = new PDF();
                    $pdf->SetMargins(15, 20, 15);
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->SetTextColor(50, 50, 50);
                    
                    // Gerando o QR Code
                    $qrData = "Codigo do Doador: $codigo_doador\nCampanha: $local_campanha\nData da Doacao: $data_agendada_formatada";
                    $qrFile = 'qrcode.png';  // Nome do arquivo onde o QR Code será salvo
                    QRcode::png($qrData, $qrFile);  // Gera o QR Code
                    
                    // Adicionando conteúdo ao PDF
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->SetTextColor(178, 34, 34);
                    $pdf->Cell(0, 10, mb_convert_encoding('COMPROVANTE DE AGENDAMENTO DE DOAÇÃO', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
                    $pdf->Ln(10);
                    
                    // Dados do doador
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(0, 10, mb_convert_encoding("Código do Doador: $codigo_doador", 'ISO-8859-1', 'UTF-8'), 0, 1);
                    $pdf->Cell(0, 10, mb_convert_encoding("Nome: " . $user['name'], 'ISO-8859-1', 'UTF-8'), 0, 1);
                    $pdf->Cell(0, 10, mb_convert_encoding("Tipo Sanguíneo: " . $user['blood_type'], 'ISO-8859-1', 'UTF-8'), 0, 1);
                    $pdf->Cell(0, 10, mb_convert_encoding("Data da Doação: $data_agendada_formatada", 'ISO-8859-1', 'UTF-8'), 0, 1);
                    $pdf->Cell(0, 10, mb_convert_encoding("Local da Campanha: $local_campanha", 'ISO-8859-1', 'UTF-8'), 0, 1);
                    
                    // Inserir o QR Code no PDF
                    $pdf->Image($qrFile, 150, 250, 30, 30);  // Ajuste a posição e o tamanho conforme necessário
                    
                    // Assinatura
                    $pdf->Ln(20);
                    $pdf->SetFont('Arial', 'I', 12);
                    $pdf->Cell(0, 10, '', 0, 1, 'C'); // Espaço vazio para centralizar
                    $pdf->Cell(0, 10, 'Assinatura da Hemoterapia', 0, 1, 'C');
                    $pdf->Cell(0, 10, '___________________________', 0, 1, 'C');
                    
                    // Gerando o PDF para download
                    $data = date('Ymd_His');
                    $filename = "Comprovante_de_agendamento_" . $data . "_" . rand(1000, 9999) . ".pdf";
                    $pdf->Output('D', $filename);  // 'D' força o download do arquivo
                    exit;
                  
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $erros[] = "Erro ao agendar doação: " . $e->getMessage();
            }
        }

        if (!empty($erros)) {
            $_SESSION['error_message'] = implode("<br>", $erros);
        }

        header("Location: schedule_donation.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Doação - Blood Place Voluntary</title>


    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600&display=swap">

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
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

        .fa-calendar-day {
            color: #D11A1A;
            font-size: 1.2em;
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


        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
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
    </style>
</head>

<body>
    <?php include_once "header.php"; ?>


    <?php if (!$perfil_completo): ?>
        <div id="profileWarningModal" class="modal" style="display: block;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div class="modal-header">
                    <h3><i class="fas fa-exclamation-triangle"></i>Dados Incompletos!</h3>
                    <p>Para agendar doações, complete seus dados:</p>
                </div>
                <ul>
                    <?php if (empty($user['birthdate'])): ?>
                        <li><i class="fas fa-tint"></i>Data de Nascimento</li>
                    <?php endif; ?>
                    <?php if (empty($user['blood_type'])): ?>
                        <li><i class="fas fa-tint"></i>Tipo Sanguíneo</li>
                    <?php endif; ?>
                    <?php if (empty($user['city'])): ?>
                        <li><i class="fas fa-city"></i>Cidade</li>
                        <li style="animation: pulse 2s infinite;">
                            <i class="fas fa-tint"></i>Data de Nascimento (Obrigatória)
                        </li>
                    <?php endif; ?>
                </ul>
                <a href="edit_profile.php" class="btn-modal">
                    <i class="fas fa-user-edit"></i>Completar Cadastro
                </a>
            </div>
        </div>
    <?php endif; ?>


    <main class="schedule-container">
        <h2>Agendar Doação</h2>


        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']);
        endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error"><?= $_SESSION['error_message'] ?></div>
        <?php unset($_SESSION['error_message']);
        endif; ?>


        <div class="schedule-sections">

            <section class="schedule-section">
                <h3>Agendar Nova Doação</h3>
                <form method="POST" action="schedule_donation.php">
                    <div class="form-group">
                        <label for="campanha_id">Campanha:</label>
                        <select name="campanha_id" id="campanha_id" required>
                            <?php foreach ($campanhas as $campanha): ?>
                                <option value="<?= $campanha['id'] ?>">
                                    <?= htmlspecialchars($campanha['local']) ?> -
                                    <?= date("d/m/Y", strtotime($campanha['data_inicio'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($user['name'] ?? '') ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Idade:</label>
                        <input type="text" value="<?= $idade ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="quantidade">Quantidade (ml):</label>
                        <input type="number" name="quantidade" min="200" max="500" value="450" required>
                    </div>

                    <div class="form-group">
                        <label for="data_agendada">Data da Doação:</label>
                        <input type="date" name="data_agendada" required>
                    </div>

                    <button type="submit" class="btn">Agendar Doação</button>
                </form>
            </section>


            <section class="schedule-section">
                <h3>Histórico de Doações</h3>
                <?php if (!empty($historico_doacoes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Quantidade</th>
                                <th>Local</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historico_doacoes as $doacao): ?>
                                <tr>
                                    <td><?= date("d/m/Y", strtotime($doacao['data_agendada'])) ?></td>
                                    <td><?= $doacao['quantidade'] ?>ml</td>
                                    <td><?= htmlspecialchars($doacao['local']) ?></td>
                                    <td><?= ucfirst($doacao['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">Nenhuma doação registrada.</p>
                <?php endif; ?>
            </section>
        </div>




    </main>

    <script>
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                item.classList.toggle('active');
            });
        });


        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('profileWarningModal');
            if (modal) {
                const closeBtn = modal.querySelector('.close');
                closeBtn.onclick = () => modal.style.display = 'none';

                window.onclick = (e) => e.target === modal && (modal.style.display = 'none');

                document.querySelector('form').addEventListener('submit', (e) => {
                    if (<?= $bloquear_agendamento ? 'true' : 'false' ?>) {
                        e.preventDefault();
                        modal.style.display = 'block';
                    }
                });
            }
        });
    </script>
    <section class="schedule-section">
        <h3>Dicas para uma Doação Segura</h3>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <i class="fas fa-tint faq-icon"></i> Beber água antes da doação?
                </div>
                <div class="faq-answer">Sim! A hidratação facilita a coleta e evita tonturas.</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fas fa-utensils faq-icon"></i> Precisa estar em jejum?
                </div>
                <div class="faq-answer">Não! Faça uma refeição leve antes.</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fas fa-running faq-icon"></i> Atividades físicas antes?
                </div>
                <div class="faq-answer">Evite exercícios intensos no dia da doação.</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fas fa-bed faq-icon"></i> Após a doação?
                </div>
                <div class="faq-answer">Descanse e evite esforços físicos.</div>
            </div>
        </div>
    </section>
    <?php include_once "footer.php"; ?>
</body>

</html>