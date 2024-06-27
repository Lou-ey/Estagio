document.addEventListener('DOMContentLoaded', function () {
    const sensorForm = document.getElementById('sensorForm');
    const sensorMessageArea = document.getElementById('sensorMessageArea');

    sensorForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_sensor.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showMessage('Sensor adicionado com sucesso.', 'success');
                        setTimeout(function() {
                            sensorMessageArea.innerHTML = '';
                        }, 2000);
                        updateTable();
                        const modal = document.getElementById('addSensorModal');
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        bootstrapModal.hide();
                        sensorForm.reset();

                    } else {
                        showMessage('Erro ao adicionar o sensor.', 'danger');
                    }
                } else {
                    showMessage('Erro na solicitação. Tente novamente mais tarde.', 'danger');
                }
            }
        };
        xhr.send(formData);
    });

    function showMessage(message, type) {
        sensorMessageArea.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }

    function updateTable() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_sensors.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const sensorTable = document.getElementById('sensorTable');
                        sensorTable.innerHTML = response.data;
                    } else {
                        showMessage('Erro ao obter os sensores.', 'danger');
                    }
                } else {
                    showMessage('Erro na solicitação. Tente novamente mais tarde.', 'danger');
                }
            }
        };
        xhr.send();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status');

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const sensorId = this.dataset.sensorId;
            const newStatus = this.value;

            // Envia uma solicitação AJAX para atualizar o status
            fetch('update_sensor_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `sensor_id=${sensorId}&new_status=${newStatus}&new_updated_at=${new Date().toISOString()}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta da rede');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Status atualizado com sucesso.');
                    showMessage('Status atualizado com sucesso.', 'success');
                } else {
                    console.error('Erro ao atualizar o status.');
                    showMessage('Erro ao atualizar o status.', 'danger');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        });
    });

    function showMessage(message, type) {
        const sensorMessageArea = document.getElementById('sensorMessageArea');
        sensorMessageArea.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }
});


