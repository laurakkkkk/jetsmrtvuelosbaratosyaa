<?php
/**
 * aprobar_datos.php - Aprueba o rechaza datos con tipo de error
 * CORREGIDO - Mejor manejo de errores
 */

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$action = $_POST['action'] ?? null;
$log_id = $_POST['log_id'] ?? null;
$error_tipo = $_POST['error'] ?? null;

if (!$action || !$log_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parámetros faltantes']);
    exit;
}

$logsFile = __DIR__ . '/data/pse_logs.json';

if (!file_exists($logsFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'No logs file']);
    exit;
}

$content = file_get_contents($logsFile);
if ($content === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error reading file']);
    exit;
}

$logs = json_decode($content, true);
if (!is_array($logs)) {
    $logs = [];
}

$found = false;
foreach ($logs as $key => &$log) {
    if (isset($log['id']) && $log['id'] === $log_id) {
        if ($action === 'aprobar') {
            $log['estado'] = 'aprobado';
            $log['aprobado'] = true;
            $log['aprobado_en'] = date('Y-m-d H:i:s');
            unset($log['error_tipo']);
        } else if ($action === 'rechazar') {
            $log['estado'] = 'rechazado';
            $log['aprobado'] = false;
            $log['rechazado_en'] = date('Y-m-d H:i:s');
            if ($error_tipo) {
                $log['error_tipo'] = $error_tipo;
            }
        }
        $found = true;
        break;
    }
}
unset($log);

if (!$found) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Log no encontrado']);
    exit;
}

$json = json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error encoding JSON']);
    exit;
}

if (file_put_contents($logsFile, $json, LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error writing file']);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => $action === 'aprobar' ? 'Aprobado' : 'Rechazado'
]);
exit;
?>
