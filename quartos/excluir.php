<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];

try {
    if (!$numero) {
        throw new Exception('Número não informado.');
    }

    $stmt = $conn->prepare("SELECT * FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero]);
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        http_response_code(400);
        throw new Exception('Quarto não encontrado.');
    }

    if ($quarto['disponivel'] == 0) {
        http_response_code(400);
        throw new Exception('Não é possível excluir um quarto ocupado.');
    }
    
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE numero_quarto = ?");
    $stmt->execute([$numero]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($reservas) {
        http_response_code(400);
        throw new Exception('Não é possível excluir um quarto com reservas.');
    }

    $stmt = $conn->prepare("DELETE FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero]);
    
    $response = [
        'success' => true,
        'message' => 'Quarto excluído com sucesso!'
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao excluir quarto: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
