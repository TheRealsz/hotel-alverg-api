<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];
$capacidade = $data['capacidade'];
$diaria = $data['diaria'];

try {
    if (!$numero || !$capacidade || !$diaria) {
        throw new Exception('Preencha todos os campos.');
    }

    $stmt = $conn->prepare("SELECT * FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero]);
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($quarto) {
        throw new Exception('Número de quarto já cadastrado.');
    }

    if ($capacidade < 1 || $capacidade > 5) {   
        throw new Exception('Capacidade inválida.');
    }

    $stmt = $conn->prepare("INSERT INTO quartos (numero_quarto, capacidade, valor_diaria, disponivel) VALUES (?, ?, ?, 1)");
    $stmt->execute([$numero, $capacidade, $diaria]);

    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Quarto cadastrado com sucesso!'
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao cadastrar quarto: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
