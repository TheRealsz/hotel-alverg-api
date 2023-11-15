<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id_reserva = $data['id_reserva'];

$sql = "DELETE FROM reservas WHERE id_reserva = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $id_reserva);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva excluída com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao excluir reserva.'
    ];
}

echo json_encode($response);

$conn = null;