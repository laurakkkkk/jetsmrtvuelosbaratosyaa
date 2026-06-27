<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$logsFile = __DIR__ . '/data/pse_logs.json';

if (file_exists($logsFile)) {
    $json = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($logsFile, $json, LOCK_EX);
    echo json_encode(['success' => true, 'message' => 'Todos los logs eliminados']);
} else {
    echo json_encode(['success' => false, 'error' => 'No logs file']);
}
exit;
?>