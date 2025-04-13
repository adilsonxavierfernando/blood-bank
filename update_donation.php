<?php
session_start();
require 'configu.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_donated'])) {
    $donation_id = $_POST['donation_id'];

    $query = "UPDATE doacoes SET status = 'doado' WHERE id = ?";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$donation_id])) {
        $_SESSION['message'] = "Doação marcada como doada com sucesso!";
    } else {
        $_SESSION['message'] = "Erro ao atualizar a doação.";
    }
}

header("Location: view_stock_donations.php"); 
exit;
?>
