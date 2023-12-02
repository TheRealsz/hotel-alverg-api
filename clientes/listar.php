<?php
require_once '../config.php';

$key = 'chave-secreta';
$method = 'aes-256-cbc';

$iv = '1234567890123456';

try {
    $sql = "SELECT * FROM clientes";
    $stmt = $conn->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($clientes as $i => $cliente) {
        $encrypted_cpf = $cliente['cpf'];
        $encrypted_cpf = base64_decode($encrypted_cpf);
        $decrypted_cpf = openssl_decrypt($encrypted_cpf, $method, $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted_cpf === false) {
            echo openssl_error_string();
        }

        $clientes[$i]['cpf'] = $decrypted_cpf;
    }

    $response = [
        'success' => true,
        'data' => $clientes
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage() || 'Erro ao buscar clientes.'
    ];
}

echo json_encode($response);

$conn = null;
