<?php
session_start();
require 'configu.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id'])) {
    $estoque_id = $_GET['id'];

   
    $query = "SELECT * FROM estoque WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$estoque_id]);
    $estoque = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($estoque) {
        $doacao_id = $estoque['doacao_id'];

        
        $deleteQuery = "DELETE FROM estoque WHERE id = ?";
        $stmt = $pdo->prepare($deleteQuery);

        if ($stmt->execute([$estoque_id])) {
          
            $updateQuery = "UPDATE doacoes SET status = 'pendente' WHERE id = ?";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([$doacao_id]);

            $_SESSION['message'] = "Doação removida do estoque com sucesso.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erro ao remover doação do estoque.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Doação não encontrada no estoque.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: view_donations.php");
    exit;
}
?>
