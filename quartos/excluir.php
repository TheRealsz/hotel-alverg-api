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
        throw new Exception('Quarto não encontrado.');
    }

    if ($quarto['disponivel'] == 0) {
        throw new Exception('Não é possível excluir um quarto ocupado.');
    }

    $stmt = $conn->prepare("DELETE FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero]);
    
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Quarto excluído com sucesso!'
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao excluir quarto: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
