<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'blood_place';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $upload_dir = 'uploads/';

   
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = $upload_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);

      
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
    }

    
    if (!empty($_FILES['cover_picture']['name'])) {
        $cover_picture = $upload_dir . basename($_FILES['cover_picture']['name']);
        move_uploaded_file($_FILES['cover_picture']['tmp_name'], $cover_picture);

        
        $stmt = $pdo->prepare("UPDATE users SET cover_picture = :cover_picture WHERE id = :id");
        $stmt->bindParam(':cover_picture', $cover_picture);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
    }

    header("Location: profile.php");
    exit;
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>