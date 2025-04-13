<?php

$host = 'localhost';
$dbname = 'blood_place';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $birthdate = $_POST['birthdate'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "Por favor, preencha todos os campos.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "As senhas não coincidem.";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "O email já está cadastrado.";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password,birthdate) VALUES (:name, :email, :password,:birthdate)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':birthdate', $birthdate);

    if ($stmt->execute()) {
       
        header('Location: edit_profile.php');
        exit;
    } else {
        echo "Erro ao registrar. Por favor, tente novamente.";
    }
}
?>
