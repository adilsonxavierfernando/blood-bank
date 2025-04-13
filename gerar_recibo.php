<?php
require('fpdf/fpdf.php'); 

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Recibo de Doacao de Sangue', 0, 1, 'C');
        $this->Ln(5);
    }
}


$nome = 'Joao da Silva';
$documento = '123.456.789-00';
$data = '10/03/2025';
$hora = '14:30';
$local = 'Hospital Central';
$tipo_sanguineo = 'O+';
$codigo_agendamento = 'AG123456';

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);


$pdf->Cell(0, 10, 'Recibo para o Hemocentro', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, "Nome: $nome", 0, 1);
$pdf->Cell(0, 10, "Documento: $documento", 0, 1);
$pdf->Cell(0, 10, "Data da Doacao: $data", 0, 1);
$pdf->Cell(0, 10, "Hora: $hora", 0, 1);
$pdf->Cell(0, 10, "Local: $local", 0, 1);
$pdf->Cell(0, 10, "Tipo Sanguineo: $tipo_sanguineo", 0, 1);
$pdf->Cell(0, 10, "Codigo de Agendamento: $codigo_agendamento", 0, 1);
$pdf->Ln(20);
$pdf->Cell(0, 10, 'Assinatura do Hemocentro: _______________________', 0, 1);

$pdf->AddPage();


$pdf->Cell(0, 10, 'Recibo para o Doador', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, "Nome: $nome", 0, 1);
$pdf->Cell(0, 10, "Documento: $documento", 0, 1);
$pdf->Cell(0, 10, "Data da Doacao: $data", 0, 1);
$pdf->Cell(0, 10, "Hora: $hora", 0, 1);
$pdf->Cell(0, 10, "Local: $local", 0, 1);
$pdf->Ln(20);
$pdf->Cell(0, 10, 'Assinatura do Doador: __________________________', 0, 1);


$pdf->Output('D', 'recibo_doacao.pdf');
?>
