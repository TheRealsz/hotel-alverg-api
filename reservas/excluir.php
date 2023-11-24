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

    if ($reserva['data_saida'] == date('Y-m-d')) {
        throw new Exception('Reserva já finalizada.');
    }

    $stmt = $conn->prepare("UPDATE quartos SET disponivel = 1 WHERE numero_quarto = ?");
    $stmt->execute([$reserva['numero_quarto']]);

    $stmt = $conn->prepare("UPDATE clientes SET hospedado = 0 WHERE id_cliente = ?");
    $stmt->execute([$reserva['id_cliente']]);

    $stmt = $conn->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_reserva]);

    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva excluída com sucesso!'
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao excluir reserva: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
