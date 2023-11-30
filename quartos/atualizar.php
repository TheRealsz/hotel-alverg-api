<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];
$capacidade = $data['capacidade'];
$diaria = $data['diaria'];

try {
    if (!$numero) {
        throw new Exception('Número não informado.');
    }

    if (!$capacidade || !$diaria) {
        throw new Exception('Preencha todos os campos.');
    }

    $stmt = $conn->prepare("SELECT * FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero]);
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        http_response_code(400);
        throw new Exception('Quarto não encontrado.');
    }

    if ($capacidade < 1 || $capacidade > 5) {
        http_response_code(400);
        throw new Exception('Capacidade inválida.');
    }
    
    $disponivel = $quarto['disponivel'];

    $stmt = $conn->prepare("UPDATE quartos SET capacidade = ?, valor_diaria = ?, disponivel = ? WHERE numero_quarto = ?");
    $stmt->execute([$capacidade, $diaria, $disponivel, $numero]);

    $response = [
        'success' => true,
        'message' => 'Quarto atualizado com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao atualizar quarto: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
