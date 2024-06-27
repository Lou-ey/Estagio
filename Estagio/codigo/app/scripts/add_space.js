document.addEventListener('DOMContentLoaded', function () {
    const spaceForm = document.getElementById('spaceForm');
    const spaceMessageArea = document.getElementById('spaceMessageArea');

    spaceForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this); // Obter os dados do formulário

        // Fazer uma solicitação para adicionar o espaço
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_space.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // A resposta do PHP não é JSON, então não precisa ser analisada
                    showMessage(xhr.responseText, 'success');
                    setTimeout(function() {
                        spaceMessageArea.innerHTML = '';
                    }, 2000);
                    const modal = document.getElementById('addSpaceModal');
                    const bootstrapModal = bootstrap.Modal.getInstance(modal);
                    bootstrapModal.hide();
                    spaceForm.reset();
                } else {
                    showMessage('Erro na solicitação. Tente novamente mais tarde.', 'danger');
                    setTimeout(function() {
                        spaceMessageArea.innerHTML = '';
                    }, 2000);
                }
            }
        };
        xhr.send(formData);
    });

    // Função para exibir mensagens
    function showMessage(message, type) {
        spaceMessageArea.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }
});



