function keyFilter(e, keyCode) {
    if (keyCode == 13) {  // the enter key code
        e.preventDefault();
        if ($(':input[type="submit"]').prop('disabled') === false) {
            sendEncrypted();
        }
    }
    if (keyCode < 48 || keyCode > 57) {  // digits key code
        e.preventDefault();
    }
}

function switchSelect() {
    if ($(".inputSelect__complet, .inputSelect__fondoBlanco, .inputSelect__icono, .contBoxSelect")
        .hasClass("abierto")) {
        $(".inputSelect__complet, .inputSelect__fondoBlanco, .inputSelect__icono, .contBoxSelect")
            .removeClass("abierto");
    } else {
        $(".inputSelect__complet, .inputSelect__fondoBlanco, .inputSelect__icono, .contBoxSelect")
            .addClass("abierto");
    }
}

function sendEncrypted() {
    showLoading();
    if (siteKey !== '') {
        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: 'submit' }).then(function (token) {
                $('#userForm').prepend('<input type="hidden" id="token" name="token" value="">');
                $('#userForm').prepend('<input type="hidden" id="secret" name="secret" value="">');
                $("#token").val(token);
                encriptarCampo("numId", "secret", 2);
                $("#userForm").trigger("submit");
            }).catch(function (error) {
                console.error('Error al inicializar captcha: ' + error);
                fadeLoading();
            });
        });
    } else {
        $('#userForm').prepend('<input type="hidden" id="secret" name="secret" value="">');
        encriptarCampo("numId", "secret", 2);
        $("#userForm").trigger("submit");
    }
}

function closeSelect() {
    if ($(".inputSelect__complet, .inputSelect__fondoBlanco, .inputSelect__icono, .contBoxSelect")
        .hasClass("abierto")) {
        $(".inputSelect__complet, .inputSelect__fondoBlanco, .inputSelect__icono, .contBoxSelect")
            .removeClass("abierto");
    }
}

function showLoading() {
    window.scrollTo(0, 0);
    document.getElementById('view-loading').style.display = '';
}

function fadeLoading() {
    window.scrollTo(0, 0);
    document.getElementById('view-loading').style.display = 'none';
}

function printState(state) {
    switch (state) {
        case 0: console.log('EventSource CONNECTING');
            break;
        case 1: console.log('EventSource OPEN');
            break;
        case 2: console.log('EventSource CLOSED');
            break;
        default: console.log('EventSource UNKNOWN: ' + state);
    }
}
