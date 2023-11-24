<?php

require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

$id_reserva = $data['id_reserva'];

try {

    if (!$id_reserva) {
        throw new Exception('ID da reserva não informado.');
    }

    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_reserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reserva) {
        throw new Exception('Reserva não encontrada.');
    }



} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao finalizar reserva: " . $e->getMessage()
    ];
}