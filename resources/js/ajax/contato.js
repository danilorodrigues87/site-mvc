
(function () {
    function getBaseUrl() {
        if (typeof window.urlBase === 'string' && window.urlBase !== '') {
            return window.urlBase.replace(/\/$/, '');
        }
        var el = document.body;
        return el ? (el.getAttribute('data-base-url') || '').replace(/\/$/, '') : '';
    }

    $(document).on('submit', '#contato-contact', function (event) {
        event.preventDefault();

        var base = getBaseUrl();
        if (!base) {
            Swal.fire({
                icon: 'error',
                title: 'Configuração incompleta',
                text: 'URL do site não definida. Contate o suporte.',
                confirmButtonText: 'Ok'
            });
            return;
        }

        var $form = $(this);
        var $btn = $form.find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: base + '/contato/envia-mensagem',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (response) {
                $btn.prop('disabled', false);
                if (response && response.success) {
                    Swal.fire({
                        title: 'Mensagem enviada!',
                        text: (response && response.message) || 'Obrigado. Nossa equipe responderá no e-mail que você informou.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    });
                    $form[0].reset();
                    return;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Ops! Algo deu errado',
                    text: (response && response.message) || 'Não foi possível enviar sua mensagem. Tente novamente mais tarde.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#d33'
                });
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                var msg = 'Não foi possível enviar sua mensagem. Tente novamente mais tarde.';
                if (xhr.status === 404) {
                    msg = 'Serviço de contato não encontrado (404). Verifique a configuração do servidor.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) {
                            msg = parsed.message;
                        }
                    } catch (e) {
                        if (xhr.responseText.indexOf('ERROR:') === 0) {
                            msg = 'Erro interno ao processar o formulário. O e-mail pode não ter sido enviado.';
                        }
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Ops! Algo deu errado',
                    text: msg,
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });
})();
