<?php
require('fpdf/fpdf.php');

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

$query = "SELECT * FROM reservas ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

class PDF extends FPDF {
    function Header() {
        $this->Image('images/logotipobpv.png', 90, 10, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Ln(15);
        $this->Cell(0, 10, mb_convert_encoding('Hospital Geral do Moxico', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, mb_convert_encoding('Relatório de Reservas de Doação - Blood Place Voluntary', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetLineWidth(0.5);
        $this->Line(15, 45, 195, 45);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Gerado em: ', 'ISO-8859-1', 'UTF-8') . date('d/m/Y H:i'), 0, 0, 'L');
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo(), 0, 0, 'R');
    }
}

$pdf = new PDF();
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 11);

$largura_tabela = 180;
$posicao_inicial = (190 - $largura_tabela) / 2;

$pdf->SetFillColor(0, 51, 153);
$pdf->SetTextColor(255, 255, 255);
$header = ['ID', 'Nome', 'Email', 'Telefone', 'Data', 'Horário', 'T.S', 'Doação'];
$larguras = [10, 35, 60, 20, 20, 20, 10, 25];

$pdf->SetX($posicao_inicial);
foreach ($header as $i => $coluna) {
    $pdf->Cell($larguras[$i], 10, mb_convert_encoding($coluna, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(230, 230, 230);
$fill = false;

foreach ($reservations as $reservation) {
    $pdf->SetX($posicao_inicial);
    $pdf->Cell($larguras[0], 10, mb_convert_encoding($reservation['id'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[1], 10, mb_convert_encoding($reservation['nome'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[2], 10, mb_convert_encoding($reservation['email'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[3], 10, mb_convert_encoding($reservation['telefone'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[4], 10, mb_convert_encoding($reservation['data'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[5], 10, mb_convert_encoding($reservation['horario'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[6], 10, mb_convert_encoding($reservation['tipo_sangue'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[7], 10, mb_convert_encoding($reservation['tipo_doacao'], 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', $fill);
    $fill = !$fill;
}

// Gera nome de arquivo com data e número aleatório
$randomCode = rand(1000, 9999);
$dataHoje = date('Ymd_His'); // exemplo: 20250413_151230
$filename = "reservas_existentes_{$dataHoje}_{$randomCode}.pdf";

// Força download do PDF
$pdf->Output('I', $filename, true);
exit;
?>
