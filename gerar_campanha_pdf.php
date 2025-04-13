<?php
session_start();
require('fpdf/fpdf.php');

class PDF extends FPDF {
    function Header() {
        $this->Image('images/logotipobpv.png', 10, 8, 20);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(40, 40, 40);
        $this->Cell(0, 15, mb_convert_encoding('Relatório de Campanhas Ativas', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, mb_convert_encoding('Blood Place Voluntary - Hemoterapia', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->Ln(3);
        $this->SetDrawColor(178, 34, 34);
        $this->SetLineWidth(1.2);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, mb_convert_encoding('Gerado em: ', 'ISO-8859-1', 'UTF-8') . date('d/m/Y H:i'), 0, 0, 'L');
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo(), 0, 0, 'R');
    }
}

$pdf = new PDF();
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

$larguras = [20, 55, 35, 25, 25, 30];
$headers = ['ID', 'Local', 'Cidade', 'Início', 'Fim', 'T.S.'];

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(178, 34, 34);

foreach ($headers as $i => $header) {
    $pdf->Cell($larguras[$i], 10, mb_convert_encoding($header, 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(50, 50, 50);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=blood_place", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM campanhas");

    $fill = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->SetFillColor($fill ? 250 : 245, $fill ? 250 : 245, $fill ? 250 : 245);

        $pdf->Cell($larguras[0], 10, $row['id'], 0, 0, 'C', true);
        $pdf->Cell($larguras[1], 10, mb_convert_encoding($row['local'], 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', true);
        $pdf->Cell($larguras[2], 10, mb_convert_encoding($row['cidade'], 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', true);
        $pdf->Cell($larguras[3], 10, date('d/m/Y', strtotime($row['data_inicio'])), 0, 0, 'C', true);
        $pdf->Cell($larguras[4], 10, date('d/m/Y', strtotime($row['data_fim'])), 0, 0, 'C', true);
        $pdf->Cell($larguras[5], 10, mb_convert_encoding($row['tipo_sanguineo'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C', true);

        $fill = !$fill;
    }
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Nome aleatório com data/hora
$nomeArquivo = 'relatorio_hemoterapia_' . date('Ymd_His') . '.pdf';

// Forçar download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

$pdf->Output('I', $nomeArquivo);
exit;
?>
