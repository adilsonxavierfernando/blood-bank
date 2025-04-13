<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = $_POST['reserva_id'];
    $status = $_POST['status'];

    $host = 'localhost';
    $dbname = 'blood_place';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "UPDATE reservas SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['status' => $status, 'id' => $reserva_id]);

        $_SESSION['success_message'] = "Status da reserva atualizado com sucesso!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erro ao atualizar o status: " . $e->getMessage();
    }

    header("Location: view_reservations.php");
    exit;
}
?>