<?php
require('fpdf/fpdf.php');
require_once 'configu.php';

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

class PDF extends FPDF {
    function Header() {
        $this->Image('images/logotipobpv.png', 90, 10, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Ln(15);
        $this->Cell(0, 10, mb_convert_encoding('Hospital Geral do Moxico', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, mb_convert_encoding('Lista de Doadores - Blood Place Voluntary', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
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

$pdf->SetFillColor(0, 51, 153);
$pdf->SetTextColor(255, 255, 255);
$header = ['ID', 'Nome', 'Email', 'Telefone', 'T.S.', 'Idade'];
$larguras = [15, 50, 50, 30, 20, 20];
$pdf->SetX(15);
foreach ($header as $i => $coluna) {
    $pdf->Cell($larguras[$i], 10, mb_convert_encoding($coluna, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(230, 230, 230);
$fill = false;

try {
    $query = "SELECT d.id, d.nome, d.email, d.telefone, u.blood_type, TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) AS idade FROM doadores d JOIN users u ON d.email = u.email ORDER BY d.data_doacao DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $doadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($doadores as $doador) {
        $pdf->SetX(15);
        $pdf->Cell($larguras[0], 10, mb_convert_encoding($doador['id'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Cell($larguras[1], 10, mb_convert_encoding($doador['nome'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Cell($larguras[2], 10, mb_convert_encoding($doador['email'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Cell($larguras[3], 10, mb_convert_encoding($doador['telefone'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Cell($larguras[4], 10, mb_convert_encoding($doador['blood_type'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Cell($larguras[5], 10, mb_convert_encoding($doador['idade'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
        $pdf->Ln();
        $fill = !$fill;
    }
} catch (PDOException $e) {
    die("Erro ao buscar doadores: " . $e->getMessage());
}

// Gera nome de arquivo com data e número aleatório
$randomCode = rand(1000, 9999);
$dataHoje = date('Ymd_His'); // exemplo: 20250413_151230
$filename = "Lista_de_Doadores_{$dataHoje}_{$randomCode}.pdf";

// Força download do PDF
$pdf->Output('D', $filename);
exit;

?>
