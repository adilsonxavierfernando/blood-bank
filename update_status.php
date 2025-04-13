<?php
session_start();
require 'configu.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doacao_id = $_POST['doacao_id'];
    $novo_status = $_POST['status'];

    $query = "UPDATE doacoes SET status = :status WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':status', $novo_status);
    $stmt->bindParam(':id', $doacao_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Status atualizado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar o status.";
    }
}


header("Location: view_stock_donations.php");
exit;
