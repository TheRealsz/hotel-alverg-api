<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id = $data['id'];
try {
    if (!$id) {
        throw new Exception('ID não informado.');
    }
    $stmt =  $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente) {
        throw new Exception('Cliente não encontrado.');
    }

    $stmt = $conn->prepare("SELECT hospedado FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $hospedado = $stmt->fetchColumn();
    if ($hospedado) {
        throw new Exception('Não é possivel excluir um cliente hospedado.');
    }

    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($reservas) {
        throw new Exception('Não é possivel excluir um cliente com reservas.');
    }

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Cliente excluído com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao excluir cliente: " .$e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
