<?php
require('fpdf/fpdf.php');
require 'configu.php';

$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$status = $_POST['status'] ?? null;

$conditions = [];
$params = [];

if ($start_date) {
    $conditions[] = "d.data >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $conditions[] = "d.data <= ?";
    $params[] = $end_date;
}
if ($status) {
    $conditions[] = "d.status = ?";
    $params[] = $status;
}

$blood_type = $_POST['blood_type'] ?? null;
if ($blood_type) {
    $conditions[] = "u.blood_type = ?";
    $params[] = $blood_type;
}

$whereClause = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('images/logotipobpv.png', 90, 10, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Ln(15);
        $this->Cell(0, 10, mb_convert_encoding('Hospital Geral do Moxico', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, mb_convert_encoding('Relatório de Doações - Blood Place Voluntary - Hemoterapia', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetLineWidth(0.5);
        $this->Line(15, 45, 195, 45);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Gerado em: ', 'ISO-8859-1', 'UTF-8') . date('d/m/Y H:i'), 0, 0, 'L');
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo(), 0, 0, 'R');
    }
}

$query = "SELECT d.id, u.name AS doador_name, u.blood_type, c.local AS campanha_local, 
                 d.quantidade, d.data, d.status 
          FROM doacoes d
          JOIN users u ON d.doador_id = u.id
          JOIN campanhas c ON d.campanha_id = c.id
          $whereClause";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF();
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 11);

$largura_tabela = 180;
$posicao_inicial = (190 - $largura_tabela) / 2;

$pdf->SetFillColor(0, 51, 153);
$pdf->SetTextColor(255, 255, 255);
$header = ['ID', 'Doador', 'T.S.', 'Campanha', 'Qtd. (ml)', 'Data', 'Status'];
$larguras = [15, 40, 10, 50, 25, 40, 20];

$pdf->SetX($posicao_inicial);
foreach ($header as $i => $coluna) {
    $pdf->Cell($larguras[$i], 10, mb_convert_encoding($coluna, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(230, 230, 230);
$fill = false;

foreach ($result as $row) {
    $pdf->SetX($posicao_inicial);
    $pdf->Cell($larguras[0], 10, $row['id'], 1, 0, 'C', $fill);
    $pdf->Cell($larguras[1], 10, mb_convert_encoding($row['doador_name'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[2], 10, $row['blood_type'], 1, 0, 'C', $fill);
    $pdf->Cell($larguras[3], 10, mb_convert_encoding($row['campanha_local'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($larguras[4], 10, $row['quantidade'], 1, 0, 'C', $fill);
    $pdf->Cell($larguras[5], 10, $row['data'], 1, 0, 'C', $fill);
    $pdf->Cell($larguras[6], 10, mb_convert_encoding($row['status'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Ln();
    $fill = !$fill;
}

// Criar gráfico
$imageFile = "grafico_doacoes.png";
$largura = 500;
$altura = 300;
$img = imagecreate($largura, $altura);

$fundo = imagecolorallocate($img, 255, 255, 255);
$preto = imagecolorallocate($img, 0, 0, 0);
$azul = imagecolorallocate($img, 0, 51, 153);

$bloodTypeQuery = "SELECT u.blood_type, SUM(d.quantidade) as total 
                   FROM doacoes d
                   JOIN users u ON d.doador_id = u.id
                   $whereClause
                   GROUP BY u.blood_type";

$bloodStmt = $pdo->prepare($bloodTypeQuery);
$bloodStmt->execute($params);
$bloodTypeData = $bloodStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$dados = [];
$maxValor = 0;

foreach ($bloodTypeData as $tipo => $qtd) {
    $dados[$tipo] = $qtd;
    if ($qtd > $maxValor) {
        $maxValor = $qtd;
    }
}

imageline($img, 50, 250, 450, 250, $preto);
imageline($img, 50, 250, 50, 50, $preto);

$x = 70;
$barWidth = 40;
foreach ($dados as $tipo => $qtd) {
    $alturaBarra = ($qtd / $maxValor) * 180;
    imagefilledrectangle($img, $x, 250 - $alturaBarra, $x + $barWidth, 250, $azul);
    imagestring($img, 5, $x + 5, 255, $tipo, $preto);
    $x += 60;
}

imagepng($img, $imageFile);
imagedestroy($img);

$pdf->Ln(10);
$pdf->Cell(0, 10, mb_convert_encoding('Gráfico de Doações por Tipo Sanguíneo', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Image($imageFile, 30, null, 150);

// Gerar nome de arquivo com data e aleatório
$filename = 'relatorio_doacoes_' . date('Ymd_His') . '_' . random_int(1000, 9999) . '.pdf';
$pdf->Output('D', $filename);
exit;
