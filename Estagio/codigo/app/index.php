<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Event Comfort Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
</head>

<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#loc">
                <img src="http://digitalprogression.pt/img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
                DigitalProgression
            </a>
            <?php if (isset($_SESSION['id'])) {
                echo '<div class="justify-content-between">
                    <a class="btn btn-outline-light mx-2" href="organizador.php">Organizador</a>
                    <a class="btn btn-outline-light mx-2" href="historico.php">Histórico</a>';
                if (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin']) {
                    echo '<a class="btn btn-outline-light mx-2" href="superadmin.php">Superadmin</a>';
                }
                echo '</div>
                <div>
                    <a class="btn btn-outline-light" href="logout.php">Log out</a>
                    <a class="btn btn-outline-light" href="http://digitalprogression.pt/" target="_blank">Saber Mais</a>
                </div>';
            } else {
                echo '<div>
                        <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                        <a class="btn btn-outline-light" href="http://digitalprogression.pt/" target="_blank">Saber Mais</a>
                    </div>';
            }
            ?>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="loginModalLabel">Aviso</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    O login é restrito aos organizadores do evento. Se és um organizador, clica em "Compreendo e Prosseguir" para efetuar o login.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href=' login.php'">Compreendo e Prosseguir</button>
                </div>
            </div>
        </div>
    </div>



    <div class="container mt-4">
        <h1>Transforme o seu conforto em prioridade na FACIT!</h1>
        <p class="lead mb-4">
            Graças à iniciativa do <strong>Município de Tábua</strong> e à inovação da start-up <strong>DigitalProgression</strong>, agora monitorizamos as condições de conforto em tempo real. Porque a sua satisfação é o nosso progresso.
        </p>
        <div class="row" id="sensorCards">
            <!-- Sensor cards will be added here dynamically -->
        </div>

        <!-- Here are the chart canvases -->

        <div class="row mt-4">
            <div class="col-lg-6">
                <h5>Temperatura</h5>
                <canvas id="tempChart"></canvas>
            </div>
            <div class="col-lg-6">
                <h5>Humidade</h5>
                <canvas id="humidityChart"></canvas>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col"><a href="https://digitalprogression.pt/" target="_blank"><img src="img/img01.png" class="img-fluid"></a></div>
            <div class="col"><a href="https://www.cm-tabua.pt/" target="_blank"><img src="img/img02.png" class="img-fluid"></a></div>
            <div class="col"><a href="https://www.cm-tabua.pt/viver/feiras-e-mercados/facit/" target="_blank"><img src="img/img03.png" class="img-fluid"></a></div>
            <div class="col"><a href="https://espaco-cultiva.pt/home/" target="_blank"><img src="img/img04.png" class="img-fluid"></a></div>
        </div>

        <footer class="text-muted m-4">
            <hr>
            2023 &copy; Todos os direitos reservados a <a href="https://digitalprogression.pt/" target="_blank">DigitalProgression, Lda.</a>
        </footer>
    </div>

    <script>
        // Here, we initialize the Chart.js charts with the 'time' type for the x-axis.
        // If you're using a version of Chart.js older than 3.0.0, you may need to use xAxes: [{type: 'time'}] instead.
        var charts = {
            'tempChart': new Chart(document.getElementById('tempChart'), {
                type: 'line',
                data: {
                    datasets: []
                },
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            display: true,
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            }),
            'humidityChart': new Chart(document.getElementById('humidityChart'), {
                type: 'line',
                data: {
                    datasets: []
                },
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            display: true,
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            })
        };

        // Here, we define a helper function to get the dataset for a particular sensor. If the dataset doesn't exist yet, we create it.
        function getDataset(chart, sensorId, sensorLocation) {
            for (var i = 0; i < chart.data.datasets.length; i++) {
                if (chart.data.datasets[i].label === 'Sensor ' + sensorId + ' (' + sensorLocation + ')') {
                    return chart.data.datasets[i];
                }
            }
            var newDataset = {
                label: 'Sensor ' + sensorId + ' (' + sensorLocation + ')',
                data: [],
                borderColor: getRandomColor(),
                fill: false
            };
            chart.data.datasets.push(newDataset);
            return newDataset;
        }

        // Here, we define a helper function to get a random color for each sensor dataset.
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // This function fetches the latest sensor data, updates the charts, and schedules the next fetch.
        function fetchData() {
            $.ajax({
                url: 'fetch_data.php',
                method: 'GET',
                success: function(response) {
                    $('#sensorCards').empty();
                    var sensorData = JSON.parse(response);
                    for (var i = 0; i < sensorData.length; i++) {
                        var timestamp = new Date(sensorData[i].timestamp).getTime();

                        var tempDataset = getDataset(charts['tempChart'], sensorData[i].id, sensorData[i].location);
                        tempDataset.data.push({
                            x: timestamp,
                            y: sensorData[i].temperature
                        });

                        var humidityDataset = getDataset(charts['humidityChart'], sensorData[i].id, sensorData[i].location);
                        humidityDataset.data.push({
                            x: timestamp,
                            y: sensorData[i].humidity
                        });

                        var card = createSensorCard(sensorData[i]);
                        $('#sensorCards').append(card);
                    }
                    for (var chartName in charts) {
                        charts[chartName].update();
                    }
                },
                complete: function() {
                    setTimeout(fetchData, 5000);
                }
            });
        }

        // Here, we define a function to create a sensor card dynamically.
        function createSensorCard(sensorData) {
            var card =
                `<div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">${sensorData.location}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Sensor ID: ${sensorData.id}</h6>
                            <div class="card-text">
                                <div class="d-flex justify-content-between">
                                    <div><strong><i class="fas fa-thermometer-half"></i> Temperatura</strong></div>
                                    <div>${sensorData.temperature} °C ${getFeedback('temperature', sensorData.temperature)}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div><strong><i class="fas fa-tint"></i> Humidade</strong></div>
                                    <div>${sensorData.humidity} % ${getFeedback('humidity', sensorData.humidity)}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div><strong><i class="fas fa-volume-up"></i> Nível de Ruído</strong></div>
                                    <div>${getFeedback('noise', sensorData.noise)}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div><strong><i class="fas fa-cloud"></i> Qualidade do Ar</strong></div>
                                    <div>${getFeedback('air_quality', sensorData.air_quality)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            return card;
        }

        // This function gives feedback on the sensor reading based on some predetermined conditions.
        function getFeedback(sensorType, sensorValue) {
            var feedback = '';

            switch (sensorType) {
                case 'temperature':
                    if (sensorValue < 20) {
                        feedback = '<span class="text-info" title="Abaixo do intervalo recomendado"><i class="fas fa-arrow-down" aria-hidden="true"></i></span>';
                    } else if (sensorValue > 25) {
                        feedback = '<span class="text-warning" title="Acima do intervalo recomendado"><i class="fas fa-arrow-up" aria-hidden="true"></i></span>';
                    } else {
                        feedback = '<span class="text-success" title="Dentro do intervalo recomendado"><i class="fas fa-check" aria-hidden="true"></i></span>';
                    }
                    break;
                case 'humidity':
                    if (sensorValue < 30) {
                        feedback = '<span class="text-info" title="Abaixo do intervalo recomendado"><i class="fas fa-arrow-down" aria-hidden="true"></i></span>';
                    } else if (sensorValue > 50) {
                        feedback = '<span class="text-warning" title="Acima do intervalo recomendado"><i class="fas fa-arrow-up" aria-hidden="true"></i></span>';
                    } else {
                        feedback = '<span class="text-success" title="Dentro do intervalo recomendado"><i class="fas fa-check" aria-hidden="true"></i></span>';
                    }
                    break;
                case 'noise':
                    if (sensorValue < 1) {
                        feedback = '<span class="text-warning" title="Acima do intervalo recomendado"><i class="fas fa-arrow-up" aria-hidden="true"></i></span>';
                    } else {
                        feedback = '<span class="text-success" title="Dentro do intervalo recomendado"><i class="fas fa-check" aria-hidden="true"></i></span>';
                    }
                    break;
                case 'air_quality':
                    if (sensorValue < 1) {
                        feedback = '<span class="text-warning" title="Acima do intervalo recomendado"><i class="fas fa-arrow-up" aria-hidden="true"></i></span>';
                    } else {
                        feedback = '<span class="text-success" title="Dentro do intervalo recomendado"><i class="fas fa-check" aria-hidden="true"></i></span>';
                    }
                    break;
                default:
                    feedback = 'Invalid sensor type';
            }

            return feedback;
        }

        $(document).ready(function() {
            fetchData();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>