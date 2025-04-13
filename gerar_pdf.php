<?php
require('fpdf/fpdf.php'); 

if (isset($_GET['reserva_id'])) {
    $reserva_id = $_GET['reserva_id'];

 
    $conn = new mysqli('localhost', 'root', 'root', 'blood_place');
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reserva_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reserva = $result->fetch_assoc();
    $stmt->close();
    $conn->close();


    class PDF extends FPDF {
        function Header() {

            $this->Image('images/logotipobpv.png', 10, 10, 30); 
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, utf8_decode('Comprovante de Reserva de Sangue'), 0, 1, 'C');
            $this->SetFont('Arial', 'I', 12);
            $this->Cell(0, 10, utf8_decode('Blood Place Voluntary'), 0, 1, 'C');
            $this->Ln(10);
            $this->SetLineWidth(0.5);
            $this->Line(10, 40, 200, 40); 
            $this->Ln(15);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode('Gerado em: ') . date('d/m/Y H:i'), 0, 0, 'L');
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'R');
        }
    }

    $pdf = new PDF();
    $pdf->SetMargins(15, 20, 15);
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

  
    $headerColor = array(0, 51, 153); 
    $rowColor = array(230, 230, 230); 

    
    $pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 10, utf8_decode('Informações da Reserva'), 1, 1, 'C', true);
    $pdf->Ln(5);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor($rowColor[0], $rowColor[1], $rowColor[2]);

   
    $dados = array(
        'Nome' => $reserva['nome'],
        'E-mail' => $reserva['email'],
        'Telefone' => $reserva['telefone'],
        'Data' => $reserva['data'],
        'Horário' => $reserva['horario'],
        'Tipo Sanguíneo' => $reserva['tipo_sangue'],
        'Tipo de Doação' => $reserva['tipo_doacao'],
        'Setor' => $reserva['setor'],
        'Status' => $reserva['status']
    );

    foreach ($dados as $label => $valor) {
        $pdf->Cell(50, 10, utf8_decode($label), 1, 0, 'L', true);
        $pdf->Cell(0, 10, utf8_decode($valor), 1, 1, 'L');
    }

    if (!empty($reserva['observacoes'])) {
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Observações:'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, utf8_decode($reserva['observacoes']));
    }

 
    $pdf->Output('D', 'comprovante_reserva.pdf');
}
?>