<?php
require 'configu.php';

header('Content-Type: application/json');

try {
    $tipo = $_GET['tipo'] ?? '';
    
    $query = "SELECT d.id, u.name, d.quantidade 
              FROM doacoes d
              JOIN users u ON d.doador_id = u.id
              WHERE u.blood_type = ? 
              AND d.status = 'estoque' 
              AND d.quantidade > 0
              ORDER BY d.data";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tipo]);
    $doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $options = '<option value="">Selecione a doação</option>';
    foreach ($doacoes as $doacao) {
        $options .= sprintf(
            '<option value="%d" data-quantidade="%d">%s - %d ml</option>',
            $doacao['id'],
            $doacao['quantidade'],
            htmlspecialchars($doacao['name']),
            $doacao['quantidade']
        );
    }

    echo json_encode(['options' => $options]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
}
?>