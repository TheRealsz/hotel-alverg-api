<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id_cliente = $data['id_cliente'];
$nome = $data['nome'];
$email = $data['email'];
$fone = $data['fone'];
$cpf = $data['cpf'];

$key = 'chave-secreta';
$method = 'aes-256-cbc';


$iv = '1234567890123456';

try {
    if (!$id_cliente) {
        throw new Exception('ID não informado.');
    }

    if (!$nome || !$cpf || !$email || !$fone) {
        throw new Exception('Preencha todos os campos.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        http_response_code(400);
        throw new Exception('Cliente não encontrado.');
    }


    $cpf = trim($cpf);
    $cpf = str_replace(array('-', '.'), '', $cpf);

    if (strlen($cpf) != 11) {
        http_response_code(400);
        throw new Exception('O CPF deve ser preenchido no padrão 000.000.000-00.');
    }

    $encrypted_cpf = openssl_encrypt($cpf, $method, $key, OPENSSL_RAW_DATA, $iv);
    $encrypted_cpf = base64_encode($encrypted_cpf);

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE cpf = ? AND id_cliente != ?");
    $stmt->execute([$encrypted_cpf, $id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        http_response_code(400);
        throw new Exception('CPF já cadastrado.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ? AND id_cliente != ?");
    $stmt->execute([$email, $id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        http_response_code(400);
        throw new Exception('E-mail já cadastrado.');
    }

    $stmt = $conn->prepare("SELECT hospedado FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $hospedado = $cliente['hospedado'];

    $fone = preg_replace("/[^0-9]/", "", $fone);

    if (strlen($fone) != 11) {
        http_response_code(400);
        throw new Exception('O telefone deve ser preenchido no padrão (00) 00000-0000.');
    }

    $stmt = $conn->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, cpf = ?, hospedado = ? WHERE id_cliente = ?");
    $stmt->execute([$nome, $email, $fone, $encrypted_cpf, $hospedado, $id_cliente]);

    $response = [
        'success' => true,
        'message' => 'Cliente atualizado com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao atualizar cliente: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
