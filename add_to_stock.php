<?php
session_start();
require 'configu.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $checkQuery = "SELECT * FROM doacoes WHERE id = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$id]);
    $doacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($doacao) {
      
        if ($doacao['status'] === 'estoque') {
            $_SESSION['message'] = "Essa doação já foi adicionada ao estoque.";
            $_SESSION['message_type'] = "warning";
            header("Location: view_donations.php");
            exit;
        }

        
        $insertQuery = "INSERT INTO estoque (doacao_id, quantidade) VALUES (?, ?)";
        $stmt = $pdo->prepare($insertQuery);

        if ($stmt->execute([$id, $doacao['quantidade']])) {
            
            $updateQuery = "UPDATE doacoes SET status = 'estoque' WHERE id = ?";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([$id]);

            $_SESSION['message'] = "Doação adicionada ao estoque com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erro ao adicionar ao estoque.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Doação não encontrada.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: view_donations.php");
    exit;
}
?>
