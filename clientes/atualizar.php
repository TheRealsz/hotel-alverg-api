<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id_cliente = $data['id_cliente'];
$nome = $data['nome'];
$email = $data['email'];
$fone = $data['fone'];
$cpf = $data['cpf'];

try {
    if(!$id_cliente) {
        http_response_code(400);
        throw new Exception('ID não informado.');
    }

    if(!$nome || !$cpf || !$email || !$fone) {
        throw new Exception('Preencha todos os campos.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        http_response_code(400);
        throw new Exception('Cliente não encontrado.');
    }

    $hospedado = $cliente['hospedado'];

    $stmt = $conn->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, cpf = ?, hospedado = ? WHERE id_cliente = ?");
    $stmt->execute([$nome, $email, $fone, $cpf, $hospedado, $id_cliente]);

    $response = [
        'success' => true,
        'message' => 'Cliente atualizado com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao atualizar cliente: " .$e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
