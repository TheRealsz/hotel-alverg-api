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

        $formatted_cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $decrypted_cpf);
        $clientes[$i]['cpf'] = $formatted_cpf;

        $fone = $cliente['telefone'];
        $formatted_phone = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $fone);
        $clientes[$i]['telefone'] = $formatted_phone;
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
