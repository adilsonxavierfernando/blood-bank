<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
  
    $query = "SELECT n.id, n.type, n.message, n.created_at, u.name, u.profile_picture 
              FROM notifications n
              LEFT JOIN users u ON n.from_user_id = u.id
              WHERE n.to_user_id = :user_id 
              ORDER BY n.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($notifications);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>