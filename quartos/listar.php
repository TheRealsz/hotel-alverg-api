<?php
require_once '../config.php';

try {

    $sql = "SELECT * FROM quartos";
    $stmt = $conn->query($sql);

    $response = [
        'success' => true,
        'status' => 200,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => $e->getMessage() || 'Erro ao buscar quartos.'
    ];
}

echo json_encode($response);

$conn = null;
