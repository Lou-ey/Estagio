document.addEventListener('DOMContentLoaded', function () {
    const storedAlerts = sessionStorage.getItem('alerts');
    if (storedAlerts) {
        updateDropdownAlerts(JSON.parse(storedAlerts));
    }
    
    // Verificar as condições de alerta periodicamente
    setInterval(checkAlertConditions, 5000);
});

// Função para exibir um toast de alerta
function showToast(message, timestamp, sensorType, sensorValue) {
    const toastContainer = document.getElementById('alertMessages');

    const toast = document.createElement('div');
    toast.className = 'toast bg-danger text-white';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto"><i class="fas fa-thermometer-half"></i> Alerta de ${sensorType}</strong>
            <small class="text-muted">${timestamp}</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message} <br>
            <strong>Valor atual: </strong>${sensorValue}
        </div>
        <div class="d-flex justify-content-end mt-2 d-flex justify-content-center mb-2">
            <button type="button" class="btn btn-success me-2 confirm-alert" data-sensor-type="${sensorType}">Confirmar</button>
            <button type="button" class="btn btn-secondary ignore-alert" data-sensor-type="${sensorType}">Ignorar</button>
        </div>
    `;

    toastContainer.appendChild(toast);
    var bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.querySelector('.confirm-alert').addEventListener('click', function () {
        confirmAlert(sensorType);
        toast.remove();
    });

    toast.querySelector('.ignore-alert').addEventListener('click', function () {
        ignoreAlert(sensorType);
        toast.remove();
    });
}

// Função para verificar as condições de alerta e exibir os toasts se necessário
function checkAlertConditions() {
    $.ajax({
        url: 'fetch_conditions.php',
        method: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                console.error('Erro do servidor:', response.error);
                return;
            }
            if (Array.isArray(response)) {
                storeAlertsInSession(response);
                response.forEach(function (alert) {
                    const { message, timestamp, sensorType, sensorValue } = alert;
                    showToast(message, timestamp, sensorType, sensorValue);
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Erro ao verificar as condições de alerta:', error);
        }
    });
}

// Função para armazenar alertas na sessão
function storeAlertsInSession(alerts) {
    let storedAlerts = sessionStorage.getItem('alerts');
    storedAlerts = storedAlerts ? JSON.parse(storedAlerts) : [];

    alerts.forEach(function (alert) {
        if (!storedAlerts.some(storedAlert => storedAlert.message === alert.message)) {
            storedAlerts.push(alert);
        }
    });

    sessionStorage.setItem('alerts', JSON.stringify(storedAlerts));
    updateDropdownAlerts(storedAlerts);
}

// Função para atualizar os alertas no dropdown
function updateDropdownAlerts(alerts) {
    const dropdownMenu = document.getElementById('dropdownAlertas').nextElementSibling;
    dropdownMenu.innerHTML = '';

    if (alerts.length === 0) {
        const noAlertMessage = document.createElement('li');
        noAlertMessage.className = 'list-group-item text-center';
        noAlertMessage.textContent = 'Nenhum alerta disponível';
        dropdownMenu.appendChild(noAlertMessage);
    } else {
        alerts.forEach(function (alert) {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item';

            const alertContent = document.createElement('div');
            alertContent.className = 'd-flex w-100 justify-content-between';

            const alertText = document.createElement('div');
            alertText.className = 'my-2 ms-2 pe-2 border';
            alertText.innerHTML = `
                <h5 class="mb-1">${alert.sensorType}</h5>
                <p class="mb-1">${alert.message}</p>
                <small class="text-muted">${alert.timestamp}</small>
            `;

            const alertValue = document.createElement('span');
            alertValue.className = 'badge bg-danger rounded-pill ms-2 fs-6 fw-normal d-flex align-items-center my-2 me-2';
            alertValue.textContent = alert.sensorValue;

            alertContent.appendChild(alertText);
            alertContent.appendChild(alertValue);

            listItem.appendChild(alertContent);
            dropdownMenu.appendChild(listItem);
        });
    }

    // Atualizar o contador de alertas
    const alertBadge = document.querySelector('#dropdownAlertas .badge');
    alertBadge.textContent = alerts.length;
}

// Função para confirmar um alerta
function confirmAlert(sensorType) {
    let storedAlerts = sessionStorage.getItem('alerts');
    if (storedAlerts) {
        storedAlerts = JSON.parse(storedAlerts);
        storedAlerts = storedAlerts.filter(alert => alert.sensorType !== sensorType);
        sessionStorage.setItem('alerts', JSON.stringify(storedAlerts));
        updateDropdownAlerts(storedAlerts);
    }
}

// Função para ignorar um alerta
function ignoreAlert(sensorType) {
    let storedAlerts = sessionStorage.getItem('alerts');
    if (storedAlerts) {
        storedAlerts = JSON.parse(storedAlerts);
        storedAlerts = storedAlerts.filter(alert => alert.sensorType !== sensorType);
        sessionStorage.setItem('alerts', JSON.stringify(storedAlerts));
        updateDropdownAlerts(storedAlerts);
    }
}
