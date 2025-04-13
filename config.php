<?php
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'blood_place';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de daodos: " . $e->getMessage());
}
