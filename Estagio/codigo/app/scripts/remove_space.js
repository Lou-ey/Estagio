document.addEventListener('DOMContentLoaded', function() {
    const deleteSpaceButtons = document.querySelectorAll('.deleteSpace');
    const confirmDeleteButton = document.getElementById('confirmSpaceDelete');
    const spaceIdInput = document.getElementById('space_id');

    deleteSpaceButtons.forEach(button => {
        button.addEventListener('click', function() {
            const spaceId = this.getAttribute('data-id');
            spaceIdInput.value = spaceId;
        });
    });

    confirmDeleteButton.addEventListener('click', function() {
        const spaceId = spaceIdInput.value;

        fetch('remove_space.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `space_id=${spaceId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da rede');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const rowToRemove = document.querySelector(`td[data-id="${spaceId}"]`).parentNode;
                if (rowToRemove) {
                    rowToRemove.remove();
                }
                const modal = document.getElementById('removeSpaceModal');
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                bootstrapModal.hide();
                
                showMessage('Espaço removido com sucesso.', 'success');
            } else {
                const modal = document.getElementById('removeSpaceModal');
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                bootstrapModal.hide();
                showMessage('Erro ao remover o espaço.', 'danger');
            }
        })
        .catch(error => {
            const modal = document.getElementById('removeSpaceModal');
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            bootstrapModal.hide();
            console.error('Erro:', error);
            showMessage('Erro ao remover o espaço verifique se existem sensores associados a este espaço.', 'danger');
        });
    });

    // Função para exibir mensagens
    function showMessage(message, type) {
        const spaceMessageArea = document.getElementById('spaceMessageArea');
        spaceMessageArea.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }
});