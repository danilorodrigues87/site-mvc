
    // FUNÇÃO QUE EXECUTA UM CREATE OU UPDATE DE DADOS
$(document).on("submit", "#contato-contact", function(event) {
        event.preventDefault(); // Evita o envio do formulário de forma tradicional

        $.ajax({
            url: urlBase+'/contato/envia-mensagem',
            type: "POST",
            //dataType: "json", // Espera que o servidor retorne um JSON
            data: $(this).serialize(), // Serializa os dados do formulário
            success: function(response) {

             if (response) {
          
                Swal.fire({
                    title: 'Mensagem enviada com sucesso!',
                    text: 'Obrigado, entraremos em contato em breve.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });

            } else {
    
                Swal.fire({
                    icon: 'error',
                    title: 'Ops! Algo deu errado',
                    text: 'Não foi possível enviar sua mensagem, tente novamente mais tarde.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#d33'
                });
            }

             document.getElementById('c_nome').value = '';
             document.getElementById('c_email').value = '';
             document.getElementById('c_whatsapp').value = '';
             if (document.getElementById('c_escola')) document.getElementById('c_escola').value = '';
             document.getElementById('assunto').value = '';
             document.getElementById('mensagem').value = '';


        }

    });

    });