<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id_cliente = $data['id_cliente'];
try {
    if (!$id_cliente) {
        throw new Exception('ID não informado.');
    }
    
    $stmt =  $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente) {
        http_response_code(400);
        throw new Exception('Cliente não encontrado.');
    }

    $stmt = $conn->prepare("SELECT hospedado FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $hospedado = $stmt->fetchColumn();
    if ($hospedado) {
        http_response_code(400);
        throw new Exception('Não é possivel excluir um cliente hospedado.');
    }

    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($reservas) {
        http_response_code(400);
        throw new Exception('Não é possivel excluir um cliente com reservas.');
    }

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $response = [
        'success' => true,
        'message' => 'Cliente excluído com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao excluir cliente: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
