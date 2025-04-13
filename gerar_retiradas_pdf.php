<?php
require('fpdf/fpdf.php'); 
require 'configu.php';

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Acesso negado!');
}

class PDF extends FPDF {
    function Header() {
        $this->Image('images/logotipobpv.png', 90, 10, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Ln(15);
        $this->Cell(0, 10, mb_convert_encoding('Hospital Geral do Moxico', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, mb_convert_encoding('Relatório de Retiradas - Blood Place Voluntary - Hemoterapia', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
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

$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$status = $_POST['status'] ?? null;
$blood_type = $_POST['blood_type'] ?? null;

$conditions = [];
$params = [];

if ($start_date) {
    $conditions[] = "r.data_retirada >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $conditions[] = "r.data_retirada <= ?";
    $params[] = $end_date;
}
if ($status) {
    $conditions[] = "r.status = ?";
    $params[] = $status;
}
if ($blood_type) {
    $conditions[] = "u.blood_type = ?";
    $params[] = $blood_type;
}

$whereClause = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

$query = "SELECT r.*, u.name AS doador_name, u.blood_type, r.hospital, r.setor, r.paciente 
          FROM retiradas r
          JOIN doacoes d ON r.doacao_id = d.id
          JOIN users u ON d.doador_id = u.id
          $whereClause
          ORDER BY r.data_retirada DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF();
$pdf->SetMargins(2, 20, 2);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 11);

$pdf->SetFillColor(0, 51, 153); 
$pdf->SetTextColor(255, 255, 255);

$header = ['ID', 'Doador', 'T.S.', 'Paciente', 'Hospital', 'Setor', 'Qtd. (ml)', 'Data'];
$larguras = [15, 35, 10, 35, 30, 25, 25, 30];

foreach ($header as $i => $coluna) {
    $pdf->Cell($larguras[$i], 10, mb_convert_encoding($coluna, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(230, 230, 230);
$fill = false;

foreach ($history as $row) {
    $pdf->Cell($larguras[0], 8, $row['id'], 1, 0, 'C', $fill);
    $pdf->Cell($larguras[1], 8, mb_convert_encoding($row['doador_name'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[2], 8, mb_convert_encoding($row['blood_type'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[3], 8, mb_convert_encoding($row['paciente'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[4], 8, mb_convert_encoding($row['hospital'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[5], 8, mb_convert_encoding($row['setor'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[6], 8, $row['quantidade_retirada'] . ' ml', 1, 0, 'C', $fill);
    $pdf->Cell($larguras[7], 8, date('d/m/Y', strtotime($row['data_retirada'])), 1, 1, 'C', $fill);
    $fill = !$fill;
}

// Gera nome de arquivo com data e número aleatório
$randomCode = rand(1000, 9999);
$dataHoje = date('Ymd_His'); // exemplo: 20250413_151230
$filename = "relatorio_retiradas_{$dataHoje}_{$randomCode}.pdf";

// Força download do PDF
$pdf->Output('D', $filename);
?>
