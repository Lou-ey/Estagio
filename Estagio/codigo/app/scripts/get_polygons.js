document.addEventListener('DOMContentLoaded', function () {
    const canvasContainer = document.getElementById('canvasContainer');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const polygons = []; // Array para armazenar os polígonos carregados da base de dados

    // Função para carregar os polígonos da base de dados
    function loadPolygonsFromDatabase() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_polygons.php', true); // Substitua 'get_polygons.php' pelo endpoint correto
        xhr.onload = function () {
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                if (data.polygons && data.polygons.length > 0) {
                    // Adicione os polígonos carregados ao array 'polygons'
                    data.polygons.forEach(polygon => {
                        polygons.push(polygon);
                    });
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

    async function redrawCanvas() {
        // Limpa o canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Carrega a imagem da planta
        const img = new Image();
        img.onload = function () {
            // Desenha a imagem da planta no canvas
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            // Desenha os polígonos após a imagem estar no canvas
            polygons.forEach(polygon => {
                // Gerar uma cor aleatória com transparência
                const randomColor = getRandomRGBA();

                ctx.beginPath();
                ctx.strokeStyle = randomColor;
                ctx.fillStyle = randomColor; // Define a mesma cor para o preenchimento
                ctx.moveTo(polygon.points[0].x, polygon.points[0].y);
                for (let i = 1; i < polygon.points.length; i++) {
                    ctx.lineTo(polygon.points[i].x, polygon.points[i].y);
                }
                ctx.closePath();
                ctx.fill(); // Preenche o polígono
                ctx.stroke();
            });
        };
        img.src = 'uploads/5863620be6f596e41b737feb96409ecb.jpg'; // Substitua 'uploads/planta.jpg' pelo caminho da imagem da planta
    }

    // Função para gerar uma cor aleatória RGBA (com transparência)
    function getRandomRGBA() {
        const r = Math.floor(Math.random() * 256); // Componente de vermelho
        const g = Math.floor(Math.random() * 256); // Componente de verde
        const b = Math.floor(Math.random() * 256); // Componente de azul
        const alpha = 0.2;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    

    // Adiciona um evento de escuta para o movimento do mouse no canvas
    canvas.addEventListener('mousemove', function (event) {
        const x = event.offsetX;
        const y = event.offsetY;
        let isOverPolygon = false;
        let polygonInfo = '';

        // Verifica se o cursor está sobre algum polígono
        polygons.forEach(polygon => {
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
});

