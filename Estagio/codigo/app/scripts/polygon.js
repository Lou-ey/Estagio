document.addEventListener('DOMContentLoaded', function () {
    const canvasContainer = document.getElementById('canvasContainer');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const addPolygonButton = document.getElementById('addPolygon');
    const lockPolygonButton = document.getElementById('lockPolygon');
    const saveButton = document.getElementById('savePolygon');
    const saveSpaceAssociationButton = document.getElementById('saveSpaceAssociation');
    const points = [];
    const polygons = [];
    const polygonsFromDB = [];
    const allPolygonsFromDB = [];
    const polygonsBySpace = [];
    let img = new Image();
    let selectedPointIndex = -1;
    let isDragging = false;
    let isDrawing = false;
    let isLocked = false;

    function fetchAllPlantas() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_planta.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const plantas = JSON.parse(xhr.responseText);
                if (plantas.length > 0) {
                    const firstPlanta = plantas[0]; // Pega a primeira planta da lista
                    loadPlanta(firstPlanta.planta_path, firstPlanta.id);
                    const plantaSelect = document.getElementById('plantaSelect');
                    plantas.forEach(planta => {
                        const option = document.createElement('option');
                        option.value = planta.id;
                        option.textContent = planta.planta_path.split('/').pop();
                        plantaSelect.appendChild(option);
                    });

                    plantaSelect.addEventListener('change', function () {
                        const plantaId = plantaSelect.value;
                        const planta = plantas.find(planta => planta.id == plantaId);
                        if (planta) {
                            loadPlanta(planta.planta_path, planta.id);
                        }
                    });
                }
            } else {
                alert('Erro ao buscar as plantas da base de dados.');
            }
        };
        xhr.send();
    }

    function loadPlanta(imgSrc, plantaId) {
        img.onload = function () {
            canvasContainer.style.display = 'block';
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            points.length = 0;
            polygons.length = 0; // Clear any existing polygons when loading a new image
            filterPolygonsByPlant(plantaId); // Filter polygons by the selected plant
            redrawCanvas();
        };
        img.src = imgSrc;
    }

    function filterPolygonsByPlant(plantaId) {
        polygonsFromDB.length = 0; // Clear the current array
        allPolygonsFromDB.forEach(polygon => {
            if (polygon.plantId === plantaId) {
                polygonsFromDB.push(polygon);
            }
        });
    }

    async function redrawCanvas() {
        // Limpa o canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Carrega a imagem da planta
        ctx.drawImage(img, 0, 0);

        // Desenha os polígonos da base de dados
        polygonsFromDB.forEach(polygon => {
            const randomColor = getRandomRGBA();
            ctx.beginPath();
            ctx.strokeStyle = randomColor;
            ctx.strokeWidth = 2;
            ctx.fillStyle = randomColor; // Define a mesma cor para o preenchimento
            ctx.moveTo(polygon.points[0].x, polygon.points[0].y);
            for (let i = 1; i < polygon.points.length; i++) {
                ctx.lineTo(polygon.points[i].x, polygon.points[i].y);
            }
            ctx.closePath();
            ctx.fill(); // Preenche o polígono
            ctx.stroke();
        });

        // Desenha os polígonos temporários (se estiver desenhando)
        polygons.forEach(polygon => {
            ctx.beginPath();
            ctx.strokeStyle = 'blue';
            ctx.moveTo(polygon.points[0].x, polygon.points[0].y);
            for (let i = 1; i < polygon.points.length; i++) {
                ctx.lineTo(polygon.points[i].x, polygon.points[i].y);
            }
            ctx.closePath();
            ctx.stroke();
        });

        // Desenha os pontos do polígono temporário (se estiver desenhando)
        drawPoints();

        // Desenha o polígono temporário (se estiver desenhando)
        drawPolygon();
    }

    // Função para gerar uma cor aleatória RGBA (com transparência)
    function getRandomRGBA() {
        const r = Math.floor(Math.random() * 256); // Componente de vermelho
        const g = Math.floor(Math.random() * 256); // Componente de verde
        const b = Math.floor(Math.random() * 256); // Componente de azul
        const alpha = 0.2;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Função para verificar se um ponto está dentro de um polígono
    function isPointInPolygon(point, polygonPoints) {
        let isInside = false;
        let j = polygonPoints.length - 1;
        for (let i = 0; i < polygonPoints.length; i++) {
            if ((polygonPoints[i].y > point.y) !== (polygonPoints[j].y > point.y) &&
                (point.x < (polygonPoints[j].x - polygonPoints[i].x) * (point.y - polygonPoints[i].y) / (polygonPoints[j].y - polygonPoints[i].y) + polygonPoints[i].x)) {
                isInside = !isInside;
            }
            j = i;
        }
        return isInside;
    }

    // Função para exibir a tooltip
    function showTooltip(event, message) {
        let tooltip = document.getElementById('tooltip');
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.classList.add('flex-column');
            tooltip.id = 'tooltip';
            tooltip.style.position = 'absolute';
            tooltip.style.background = 'rgba(0, 0, 0, 0.7)';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px';
            tooltip.style.borderRadius = '5px';
            document.body.appendChild(tooltip);
        }
        tooltip.style.display = 'block';
        tooltip.style.left = event.pageX + 0 + 'px'; // Ajusta a posição horizontal da tooltip
        tooltip.style.top = event.pageY + -35 + 'px'; // Ajusta a posição vertical da tooltip
        tooltip.textContent = message;
    }

    // Função para ocultar a tooltip
    function hideTooltip() {
        const tooltip = document.getElementById('tooltip');
        if (tooltip) {
            tooltip.style.display = 'none';
        }
    }

    // Oculta a tooltip quando o cursor sai do canvas
    canvas.addEventListener('mouseleave', hideTooltip);

    // Chama a função para carregar os polígonos da base de dados
    loadPolygonsFromDatabase();

    function loadPolygonsFromDatabase() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_polygons.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                if (data.polygons && data.polygons.length > 0) {
                    // Armazena todos os polígonos no array allPolygonsFromDB
                    data.polygons.forEach(polygon => {
                        allPolygonsFromDB.push(polygon);
                    });
                    // Filtra os polígonos pela planta atualmente selecionada
                    const plantaSelect = document.getElementById('plantaSelect');
                    const plantaId = plantaSelect.value;
                    filterPolygonsByPlant(plantaId);
                    // Redesenha o canvas após carregar os polígonos
                    redrawCanvas();
                } else {
                    console.log('Não foram encontrados polígonos na base de dados.');
                }
            } else {
                console.error('Erro ao carregar polígonos da base de dados:', xhr.statusText);
            }
        };
        xhr.send();
    }

    saveButton.addEventListener('click', function () {
        const spaceInfoTable = document.getElementById('spaceInfoTable');
        const rows = spaceInfoTable.getElementsByTagName('tr');
        const plantSelect = document.getElementById('plantaSelect');
        const plantId = plantSelect.value;

        const polygonsToSend = [];
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const spaceId = row.getAttribute('data-space-id');
            const spaceName = row.cells[1].textContent;
            const sensorIds = row.cells[2].textContent.split(',').map(id => parseInt(id.trim()));
            const points = JSON.parse(row.cells[3].textContent);

            polygonsToSend.push({
                plantId: plantId,
                spaceId: spaceId,
                spaceName: spaceName,
                sensorIds: sensorIds,
                points: points
            });
        }

        savePolygonsToDatabase(polygonsToSend);

        for (let i = rows.length - 1; i > 0; i--) {
            spaceInfoTable.deleteRow(i);
        }

        window.location.reload();
    });

    function savePolygonsToDatabase(polygons) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_polygon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                alert('Polígonos salvos com sucesso.');
            } else {
                console.error('Erro ao salvar os polígonos no banco de dados:', xhr.statusText);
                alert('Erro ao salvar os polígonos no banco de dados. Por favor, tente novamente.');
            }
        };

        xhr.send(JSON.stringify(polygons));
    }


    // Função para adicionar uma linha de informações do espaço
    function addSpaceInfoRow(id, name, sensorIds) {
        const spaceInfoTable = document.getElementById('spaceInfoTable');
        const newRow = spaceInfoTable.insertRow();
        newRow.setAttribute('data-space-id', id);
        const idCell = newRow.insertCell(0);
        const nameCell = newRow.insertCell(1);
        const sensorIdsCell = newRow.insertCell(2);
        const pointsCell = newRow.insertCell(3);
        const actionsCell = newRow.insertCell(4);

        idCell.textContent = id;
        nameCell.textContent = name;
        sensorIdsCell.textContent = sensorIds.join(', ');
        pointsCell.textContent = JSON.stringify(polygons[polygons.length - 1].points);

        actionsCell.innerHTML = `<button type="button" class="btn btn-danger btn-sm remove-space-btn" data-space-id="${id}">Remover</button>`;
        document.getElementById('space_id').value = '';
        document.getElementById('space_name').value = '';
        document.getElementById('sensor_ids').value = '';

        const modal = bootstrap.Modal.getInstance(document.getElementById('polygonModal'));
        modal.hide();

        const removeButton = actionsCell.querySelector('.remove-space-btn');
        removeButton.addEventListener('click', function () {
            const spaceId = this.getAttribute('data-space-id');
            removeSpaceInfoRow(spaceId);
            removePolygon(spaceId);
        });
    }

    // Função para remover uma linha de informações do espaço
    function removeSpaceInfoRow(spaceId) {
        const spaceInfoTable = document.getElementById('spaceInfoTable');
        const rows = spaceInfoTable.getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            if (rows[i].getAttribute('data-space-id') === spaceId) {
                spaceInfoTable.deleteRow(i);
                break;
            }
        }
    }

    // Função para remover um polígono pelo ID do espaço
    function removePolygon(spaceId) {
        const index = polygons.findIndex(polygon => polygon.space_id === spaceId);
        if (index !== -1) {
            polygons.splice(index, 1);
            redrawCanvas();
        }
    }

    // Adiciona um evento de escuta para o botão de adicionar polígono
    if (addPolygonButton) {
        addPolygonButton.addEventListener('click', function () {
            if (isDrawing) {
                //polygons.push({ id: generateUniqueId(), points: points.slice() });
                points.length = 0;
                redrawCanvas();
            }
            isDrawing = !isDrawing;
            isLocked = false;
            updateButtonStates();
        });
    }

    // Adiciona um evento de escuta para o botão de travar polígono
    if (lockPolygonButton) {
        lockPolygonButton.addEventListener('click', function () {
            if (points.length > 2) {
                isDrawing = true;
                updateButtonStates();

                const modal = new bootstrap.Modal(document.getElementById('polygonModal'));
                modal.show();
            } else {
                alert('Um polígono precisa ter pelo menos 3 pontos.');
            }
        });
    }

    // Adiciona um evento de escuta para o movimento do mouse no canvas
    canvas.addEventListener('mousedown', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;

        selectedPointIndex = points.findIndex(point => {
            const distance = Math.sqrt((point.x - x) ** 2 + (point.y - y) ** 2);
            return distance <= 5;
        });

        if (selectedPointIndex !== -1) {
            isDragging = true;
        }
    });

    // Adiciona um evento de escuta para o movimento do mouse no canvas
    canvas.addEventListener('mousemove', function (event) {
        if (selectedPointIndex !== -1 && isDragging) {
            const x = event.offsetX;
            const y = event.offsetY;
            points[selectedPointIndex].x = x;
            points[selectedPointIndex].y = y;
            redrawCanvas();
        }
    });

    // Adiciona um evento de escuta para o mouse solto no canvas
    canvas.addEventListener('mouseup', function () {
        selectedPointIndex = -1;
        isDragging = false;
    });

    // Função para atualizar os estados dos botões
    function updateButtonStates() {
        if (addPolygonButton) {
            addPolygonButton.disabled = isDrawing;
        }
        if (lockPolygonButton) {
            lockPolygonButton.disabled = !isDrawing;
        }
        if (saveButton) {
            saveButton.disabled = !isLocked; // Verifica se há algum polígono travado
        }
    }

    // Adiciona um evento de escuta para o movimento do mouse no canvas
    canvas.addEventListener('mouseleave', hideTooltip);

    // Função para gerar um ID único
    function generateUniqueId() {
        return '_' + Math.random().toString(36).substr(2, 9);
    }

    // Função para desenhar os pontos do polígono
    function drawPoints() {
        ctx.fillStyle = 'red';
        points.forEach(point => {
            ctx.beginPath();
            ctx.arc
                (point.x, point.y, 5, 0, Math.PI * 2);
            ctx.fill();
        });
    }

    // Função para desenhar o polígono temporário (se estiver desenhando)
    function drawPolygon() {
        if (isDrawing) {
            ctx.strokeStyle = 'green';
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            for (let i = 1; i < points.length; i++) {
                ctx.lineTo(points[i].x, points[i].y);
            }
            ctx.closePath();
            ctx.stroke();
        }
    }

    // Adiciona um evento de escuta para o clique no canvas
    canvas.addEventListener('click', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;

        if (isDrawing) {
            points.push({ x, y });
            redrawCanvas();
        }
    });

    // Função para adicionar um evento de escuta para o movimento do mouse no canvas
    canvas.addEventListener('mousemove', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;
        let isOverPolygon = false;
        let polygonInfo = '';

        // Verifica se o cursor está sobre algum polígono
        polygonsFromDB.forEach(polygon => {
            if (isPointInPolygon({ x, y }, polygon.points)) {
                isOverPolygon = true;
                polygonInfo = `ID do Polígono: ${polygon.id} Espaço: ${polygon.space_id}\nNome do Espaço: ${polygon.space_name}\nSensores: ${polygon.sensor_ids}`;
            }
        });

        // Exibe ou oculta a tooltip conforme necessário
        if (isOverPolygon) {
            showTooltip(event, polygonInfo);
        } else {
            hideTooltip();
        }
    });

    // Função para adicionar um evento de escuta para o botão de associar espaço
    if (saveSpaceAssociationButton) {
        saveSpaceAssociationButton.addEventListener('click', function () {
            const spaceIdInput = document.getElementById('space_id');
            const spaceNameInput = document.getElementById('space_name1');
            const sensorIdsInput = document.getElementById('sensor_ids');

            const space_id = spaceIdInput.value;
            const space_name = spaceNameInput.value;
            const sensor_ids = sensorIdsInput.value.split(',').map(id => parseInt(id.trim()));

            polygons.push({ id: generateUniqueId(), space_id: space_id, points: points.slice() });

            addSpaceInfoRow(space_id, space_name, sensor_ids);

            spaceIdInput.value = '';
            spaceNameInput.value = '';
            sensorIdsInput.value = '';

            const modal = bootstrap.Modal.getInstance(document.getElementById('polygonModal'));
            modal.hide();

            isDrawing = false; // Não estamos mais desenhando um novo polígono
            isLocked = true; // O polígono está travado
            points.length = 0; // Limpa os pontos do polígono atual
            updateButtonStates(); // Primeiro atualiza o estado dos botões
            redrawCanvas(); // Em seguida, redesenha o canvas para refletir as alterações

            console.log(polygons);
        });
    }

    // Função para adicionar um evento de escuta para o movimento do mouse no canvas
    canvas.addEventListener('mousemove', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;
        let isOverPolygon = false;
        let polygonInfo = '';

        // Verifica se o cursor está sobre algum polígono
        polygonsFromDB.forEach(polygon => {
            if (isPointInPolygon({ x, y }, polygon.points)) {
                isOverPolygon = true;
                polygonInfo = `ID do Polígono: ${polygon.id} Espaço: ${polygon.space_id}\nNome do Espaço: ${polygon.space_name}\nSensores: ${polygon.sensor_ids}`;
            }
        });

        // Exibe ou oculta a tooltip conforme necessário
        if (isOverPolygon) {
            showTooltip(event, polygonInfo);
        } else {
            hideTooltip();
        }
    });

    // Função para adicionar um evento de escuta para o clique no canvas
    canvas.addEventListener('click', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;

        if (isDrawing) {
            //points.push({ x, y });
            redrawCanvas();
        }
    });

    // Função para verificar se um ponto está dentro de um polígono
    function isPointInPolygon(point, polygonPoints) {
        let isInside = false;
        let j = polygonPoints.length - 1;
        for (let i = 0; i < polygonPoints.length; i++) {
            if ((polygonPoints[i].y > point.y) !== (polygonPoints[j].y > point.y) &&
                (point.x < (polygonPoints[j].x - polygonPoints[i].x) * (point.y - polygonPoints[i].y) / (polygonPoints[j].y - polygonPoints[i].y) + polygonPoints[i].x)) {
                isInside = !isInside;
            }
            j = i;
        }
        return isInside;
    }

    // Função para exibir a tooltip
    function showTooltip(event, message) {
        let tooltip = document.getElementById('tooltip');
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.classList.add('flex-column');
            tooltip.id = 'tooltip';
            tooltip.style.position = 'absolute';
            tooltip.style.background = 'rgba(0, 0, 0, 0.7)';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px';
            tooltip.style.borderRadius = '5px';
            document.body.appendChild(tooltip);
        }
        tooltip.style.display = 'block';
        tooltip.style.left = event.pageX + 0 + 'px'; // Ajusta a posição horizontal da tooltip
        tooltip.style.top = event.pageY + -35 + 'px'; // Ajusta a posição vertical da tooltip
        tooltip.textContent = message;
    }

    // Função para ocultar a tooltip
    function hideTooltip() {
        const tooltip = document.getElementById('tooltip');
        if (tooltip) {
            tooltip.style.display = 'none';
        }
    }

    function deletePolygon(polygonId, plantId) {
        // Remova o polígono do array polygonsFromDB e redesenhe o canvas
        const index = polygonsFromDB.findIndex(polygon => polygon.id === polygonId && polygon.plantId === plantId);
        if (index !== -1) {
            polygonsFromDB.splice(index, 1);
            redrawCanvas();
        }

        // Remova o polígono do banco de dados
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_polygon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
            } else {
                console.error('Erro ao excluir o polígono:', xhr.statusText);
            }
        }
        xhr.send('id=' + polygonId + '&plantId=' + plantId);
    }

    let deleteButton = null; // Variável para armazenar a referência ao botão de exclusão atual

    function showDeleteOption(event, polygonId, plantId) {
        // Remove o botão de exclusão anterior, se existir
        removeDeleteButton();

        // Cria um novo botão de exclusão
        deleteButton = document.createElement('button');
        deleteButton.id = 'deleteOption';
        deleteButton.style.position = 'absolute';
        deleteButton.style.left = event.pageX + 'px';
        deleteButton.style.top = event.pageY + 'px';
        deleteButton.style.background = 'red';
        deleteButton.style.color = 'white';
        deleteButton.style.padding = '5px';
        deleteButton.style.borderRadius = '5px';
        // cor da borda branca
        deleteButton.style.border = 'none';
        deleteButton.textContent = 'Eliminar Polígono';
        document.body.appendChild(deleteButton);

        deleteButton.addEventListener('click', function () {
            deletePolygon(polygonId, plantId);
            removeDeleteButton(); // Remove o botão de exclusão após clicar nele
        });

        // Remove o botão de exclusão quando o mouse sai dele
        deleteButton.addEventListener('mouseleave', function () {
            removeDeleteButton();
        });
    }

    function removeDeleteButton() {
        if (deleteButton) {
            document.body.removeChild(deleteButton);
            deleteButton = null; // Limpa a referência ao botão de exclusão
        }
    }

    canvas.addEventListener('click', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;

        polygonsFromDB.forEach(polygon => {
            if (isPointInPolygon({ x, y }, polygon.points)) {
                showDeleteOption(event, polygon.id, polygon.plantId);
            }
        });
    });

    const addPlantButton = document.getElementById('addPlantButton');

    fetchAllPlantas();

    updateButtonStates();
});
