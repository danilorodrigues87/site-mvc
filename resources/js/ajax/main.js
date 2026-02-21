const urlBase = document.body.getAttribute('data-base-url');

    // FUNÇÃO QUE EXECUTA UM CREATE OU UPDATE DE DADOS
$(document).on("submit", "#assiante-news", function(event) {
        event.preventDefault(); // Evita o envio do formulário de forma tradicional

        $.ajax({
            url: urlBase+'/contato/assinatura',
            type: "POST",
            //dataType: "json", // Espera que o servidor retorne um JSON
            data: $(this).serialize(), // Serializa os dados do formulário
            success: function(response) {

             if (response) {
          
                Swal.fire({
                    title: 'Parabéns!',
                    text: 'Sua assinatura foi realizada e voce recebera todas as nossas novidades.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });

            } else {
    
                Swal.fire({
                    icon: 'error',
                    title: 'Ops! Algo deu errado',
                    text: 'Não foi possível realizar a sua assinatura, tente novamente mais tarde.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#d33'
                });
            }

             document.getElementById('email').value = '';


        }

    });

    });