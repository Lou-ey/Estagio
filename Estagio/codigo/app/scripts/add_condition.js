document.getElementById('spaceSelect').addEventListener('change', function () {
    if (this.value !== '') {
        document.getElementById('sensorSelectDiv').style.display = 'block';
        document.getElementById('sensorTypeDiv').style.display = 'none';
        document.getElementById('operatorValueDiv').style.display = 'none';
        document.getElementById('valueInputDiv').style.display = 'none';
    } else {
        document.getElementById('sensorSelectDiv').style.display = 'none';
        document.getElementById('sensorTypeDiv').style.display = 'none';
        document.getElementById('operatorValueDiv').style.display = 'none';
        document.getElementById('valueInputDiv').style.display = 'none';
    }
});

document.getElementById('sensorSelect').addEventListener('change', function () {
    if (this.value !== '') {
        document.getElementById('sensorTypeDiv').style.display = 'block';
        document.getElementById('operatorValueDiv').style.display = 'block';
        document.getElementById('valueInputDiv').style.display = 'block';
    } else {
        document.getElementById('sensorTypeDiv').style.display = 'none';
        document.getElementById('operatorValueDiv').style.display = 'none';
        document.getElementById('valueInputDiv').style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const addConditionButton = document.getElementById('addCondition');
    const spaceSelect = document.getElementById('spaceSelect');
    const sensorSelect = document.getElementById('sensorSelect');
    const typeSelect = document.getElementById('typesSelect');
    const operatorSelect = document.getElementById('operatorSelect');
    const valueInput = document.getElementById('valueInput');
    const conditionMessageArea = document.getElementById('conditionMessageArea');
    const alertDropdown = document.getElementById('dropdownAlertas');
    const alertList = document.querySelector('.dropdown-menu');

    function checkFields() {
        return spaceSelect.value && sensorSelect.value && typeSelect.value && operatorSelect.value && valueInput.value;
    }

    function toggleButtonState() {
        addConditionButton.disabled = !checkFields();
    }

    spaceSelect.addEventListener('input', toggleButtonState);
    sensorSelect.addEventListener('input', toggleButtonState);
    typeSelect.addEventListener('input', toggleButtonState);
    operatorSelect.addEventListener('input', toggleButtonState);
    valueInput.addEventListener('input', toggleButtonState);

    addConditionButton.addEventListener('click', function (event) {
        event.preventDefault();

        const space = spaceSelect.value;
        const sensor = sensorSelect.value;
        const type = typeSelect.value;
        const operator = operatorSelect.value;
        const value = valueInput.value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_condition.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    showMessage('Condição adicionada com sucesso.', 'success');
                    setTimeout(function () {
                        conditionMessageArea.innerHTML = '';
                    }, 2000);
                    const response = JSON.parse(xhr.responseText);
                    response.forEach(alert => addAlertToDropdown(alert));
                    // Resetar os valores dos selects e do input
                    spaceSelect.selectedIndex = 0;
                    sensorSelect.selectedIndex = 0;
                    typeSelect.selectedIndex = 0;
                    operatorSelect.selectedIndex = 0;
                    valueInput.value = '';
                    toggleButtonState();
                } else {
                    showMessage('Erro ao adicionar a condição.', 'danger');
                    setTimeout(function () {
                        conditionMessageArea.innerHTML = '';
                    }, 2000);
                }
            } else {
                showMessage('Erro na solicitação. Tente novamente mais tarde.', 'danger');
                setTimeout(function () {
                    conditionMessageArea.innerHTML = '';
                }, 2000);
            }
        };
        xhr.send('sensor=' + sensor + '&type=' + type + '&condition_type=' + operator + '&value=' + value);
    });

    function showMessage(message, type) {
        conditionMessageArea.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }

    function addAlertToDropdown(alertMessage) {
        const alertItem = document.createElement('li');
        alertItem.innerHTML = '<a class="dropdown-item">' + alertMessage + '</a>';
        alertList.appendChild(alertItem);

        // Atualiza o contador de alertas
        const badge = alertDropdown.querySelector('.badge');
        const currentCount = parseInt(badge.textContent);
        badge.textContent = currentCount + 1;
    }

    toggleButtonState();
});

document.addEventListener('DOMContentLoaded', function () {
    // Fetch and render existing conditions
    fetchConditions();

    function fetchConditions() {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const conditions = JSON.parse(xhr.responseText);
                    const conditionsTableBody = document.querySelector('#existingConditionsTable tbody');
                    conditionsTableBody.innerHTML = '';
                    if (conditions.length === 0) {
                        conditionsTableBody.innerHTML = '<tr><td colspan="8">Nenhuma condição encontrada.</td></tr>';
                    } else {
                        conditions.forEach(condition => {
                            const row = `
                                <tr>
                                    <td data-id="${condition.id}">${condition.id}</td>
                                    <td>${condition.sensor_id}</td>
                                    <td>${condition.condition_type}</td>
                                    <td>${condition.value}</td>
                                    <td>${condition.created_at}</td>
                                    <td>${condition.type}</td>
                                    <td>
                                        <select class="form-select status-select" data-id="${condition.id}">
                                            <option value="active" ${condition.status === 'active' ? 'selected' : ''}>Ativa</option>
                                            <option value="inactive" ${condition.status === 'inactive' ? 'selected' : ''}>Inativa</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-condition" data-id="${condition.id}">Remover</button>
                                    </td>
                                </tr>`;
                            conditionsTableBody.innerHTML += row;
                        });

                        // Adicionar evento de alteração aos selects "Status"
                        const statusSelects = document.querySelectorAll('.status-select');
                        statusSelects.forEach(select => {
                            select.addEventListener('change', function () {
                                const conditionId = this.getAttribute('data-id');
                                const newStatus = this.value;
                                toggleConditionStatus(conditionId, newStatus);
                            });
                        });

                        // Adicionar evento de clique aos botões "Remover"
                        const deleteButtons = document.querySelectorAll('.delete-condition');
                        deleteButtons.forEach(button => {
                            button.addEventListener('click', function () {
                                const conditionId = this.getAttribute('data-id');
                                deleteCondition(conditionId);
                            });
                        });
                    }
                } else {
                    console.error('Erro ao buscar condições:', xhr.statusText);
                }
            }
        };
        xhr.open('GET', 'get_conditions.php');
        xhr.send();
    }

    function toggleConditionStatus(conditionId, newStatus) {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Status da condição atualizado com sucesso.');
                } else {
                    console.error('Erro ao atualizar status da condição:', xhr.statusText);
                }
            }
        };
        xhr.open('POST', 'toggle_condition_status.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(`id=${conditionId}&status=${newStatus}`);
    }


    function deleteCondition(conditionId) {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const result = JSON.parse(xhr.responseText);
                    if (result.success) {
                        fetchConditions();
                    } else {
                        console.error('Erro ao remover a condição:', result.error);
                    }
                } else {
                    console.error('Erro ao remover condição:', xhr.statusText);
                }
            }
        };
        xhr.open('DELETE', `delete_condition.php?id=${conditionId}`);
        xhr.send();
    }
});


