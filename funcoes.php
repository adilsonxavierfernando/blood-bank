<?php

function verificarEstoque($tipo_sanguineo, $conn) {
    $sql = "SELECT COALESCE(SUM(d.quantidade), 0) AS total_doado 
            FROM doacoes d 
            JOIN campanhas c ON d.campanha_id = c.id
            WHERE c.tipo_sanguineo = :tipo_sanguineo AND d.status = 'estoque'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tipo_sanguineo', $tipo_sanguineo, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total_doado'] == 0;
}


function criarNotificacaoFaltaEstoque($tipo_sanguineo, $conn) {
    $mensagem = "O estoque de sangue do tipo $tipo_sanguineo está baixo ou esgotado.";

  
    $queryUsers = "SELECT id FROM users WHERE blood_type = :tipo_sanguineo";
    $stmtUsers = $conn->prepare($queryUsers);
    $stmtUsers->bindParam(':tipo_sanguineo', $tipo_sanguineo, PDO::PARAM_STR);
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        foreach ($users as $user) {
            $to_user_id = $user['id'];

          
            $checkQuery = "SELECT id FROM notifications 
                           WHERE to_user_id = :to_user_id 
                           AND message = :message 
                           AND created_at >= NOW() - INTERVAL 1 DAY";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':to_user_id', $to_user_id, PDO::PARAM_INT);
            $checkStmt->bindParam(':message', $mensagem, PDO::PARAM_STR);
            $checkStmt->execute();

            if ($checkStmt->rowCount() == 0) { 
           
                $query = "INSERT INTO notifications (from_user_id, to_user_id, type, message, created_at) 
                          VALUES (:from_user_id, :to_user_id, 'message', :message, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':from_user_id', null, PDO::PARAM_NULL); 
                $stmt->bindParam(':to_user_id', $to_user_id, PDO::PARAM_INT);
                $stmt->bindParam(':message', $mensagem, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }
}


function verificarDataAgendada($conn) {
    $sql = "SELECT d.id, d.doador_id, d.data_agendada, c.local 
            FROM doacoes d
            JOIN campanhas c ON d.campanha_id = c.id
            WHERE d.data_agendada = CURDATE() AND d.status = 'agendado'";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($doacoes) {
        foreach ($doacoes as $doacao) {
            $to_user_id = $doacao['doador_id'];
            $mensagem = "Hoje é o dia da sua doação agendada no local: " . $doacao['local'];

            $checkQuery = "SELECT id FROM notifications 
                           WHERE to_user_id = :to_user_id 
                           AND message = :message 
                           AND created_at >= NOW() - INTERVAL 1 DAY";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':to_user_id', $to_user_id, PDO::PARAM_INT);
            $checkStmt->bindParam(':message', $mensagem, PDO::PARAM_STR);
            $checkStmt->execute();

            if ($checkStmt->rowCount() == 0) { 
              
                $query = "INSERT INTO notifications (from_user_id, to_user_id, type, message, created_at) 
                          VALUES (:from_user_id, :to_user_id, 'agendamento', :message, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':from_user_id', null, PDO::PARAM_NULL);
                $stmt->bindParam(':to_user_id', $to_user_id, PDO::PARAM_INT);
                $stmt->bindParam(':message', $mensagem, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }
}
?>