<?php
session_start();
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $sql = "DELETE FROM campanhas WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Campanha excluída com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";
        } else {
            $_SESSION['mensagem'] = "Erro ao excluir campanha!";
            $_SESSION['tipo_mensagem'] = "erro";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['mensagem'] = "Não é possível excluir esta campanha, pois há doações associadas a ela.";
        } else {
            $_SESSION['mensagem'] = "Ocorreu um erro ao tentar excluir a campanha.";
        }
        $_SESSION['tipo_mensagem'] = "erro";
    }
} else {
    $_SESSION['mensagem'] = "ID inválido!";
    $_SESSION['tipo_mensagem'] = "erro";
}

header("Location: view_campaigns.php");
exit();
?>
