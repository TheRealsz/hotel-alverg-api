<?php
require_once '../config.php';


try{
    $sql = "SELECT * FROM clientes";
    $stmt = $conn->query($sql);
    $response = [
        'success' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];

} catch(Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage() || 'Erro ao buscar clientes.'
    ];
}

echo json_encode($response);

$conn = null;