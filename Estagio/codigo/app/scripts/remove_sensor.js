document.addEventListener('DOMContentLoaded', function() {
    const deleteSensorButtons = document.querySelectorAll('.deleteSensor');
    const confirmDeleteButton = document.getElementById('confirmSensorDelete');
    const sensorIdInput = document.getElementById('sensor_id');

    deleteSensorButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sensorId = this.getAttribute('data-id');
            sensorIdInput.value = sensorId; // Set the sensor ID to the hidden input
        });
    });

    confirmDeleteButton.addEventListener('click', function() {
        const sensorId = sensorIdInput.value;

        fetch('remove_sensor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `sensor_id=${sensorId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da rede');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const rowToRemove = document.querySelector(`td[data-id="${sensorId}"]`).parentNode;
                if (rowToRemove) {
                    rowToRemove.remove();
                }
                const modal = document.getElementById('removeSensorModal');
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                bootstrapModal.hide();
                showMessage('Sensor removido com sucesso.', 'success');
            } else {
                showMessage('Erro ao remover o sensor.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao remover o sensor.', 'danger');
        });
    });

    function showMessage(message, type) {
        sensorMessageArea.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }
});
