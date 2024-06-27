<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="http://digitalprogression.pt/img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
                DigitalProgression
            </a>
            <div class="justify-content-between">
                <a class="btn btn-outline-light mx-2" href="organizador.php">Organizador</a>
                <a class="btn btn-outline-light mx-2" href="historico.php">Histórico</a>
            </div>
            <div>
                <a class="btn btn-outline-light" href="logout.php">Log out</a>
                <a class="btn btn-outline-light" href="http://digitalprogression.pt/" target="_blank">Saber Mais</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Histórico</h4>
                            <div>
                                <input type="text" class="form-control" placeholder="Pesquisar...">
                            </div>
                            <div>
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrar
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item sort-option" href="#" data-column="timestamp" data-order="asc">Data/Hora <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M3.47 7.78a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.751.751 0 0 1-.018 1.042.751.751 0 0 1-1.042.018L9 4.81v7.44a.75.75 0 0 1-1.5 0V4.81L4.53 7.78a.75.75 0 0 1-1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="timestamp" data-order="desc">Data/Hora <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M13.03 8.22a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L3.47 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018l2.97 2.97V3.75a.75.75 0 0 1 1.5 0v7.44l2.97-2.97a.75.75 0 0 1 1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="temperature" data-order="asc">Temperatura <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M3.47 7.78a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.751.751 0 0 1-.018 1.042.751.751 0 0 1-1.042.018L9 4.81v7.44a.75.75 0 0 1-1.5 0V4.81L4.53 7.78a.75.75 0 0 1-1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="temperature" data-order="desc">Temperatura <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M13.03 8.22a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L3.47 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018l2.97 2.97V3.75a.75.75 0 0 1 1.5 0v7.44l2.97-2.97a.75.75 0 0 1 1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="humidity" data-order="asc">Humidade <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M3.47 7.78a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.751.751 0 0 1-.018 1.042.751.751 0 0 1-1.042.018L9 4.81v7.44a.75.75 0 0 1-1.5 0V4.81L4.53 7.78a.75.75 0 0 1-1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="humidity" data-order="desc">Humidade <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M13.03 8.22a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L3.47 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018l2.97 2.97V3.75a.75.75 0 0 1 1.5 0v7.44l2.97-2.97a.75.75 0 0 1 1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="noise" data-order="asc">Ruído <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M3.47 7.78a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.751.751 0 0 1-.018 1.042.751.751 0 0 1-1.042.018L9 4.81v7.44a.75.75 0 0 1-1.5 0V4.81L4.53 7.78a.75.75 0 0 1-1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="noise" data-order="desc">Ruído <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M13.03 8.22a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L3.47 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018l2.97 2.97V3.75a.75.75 0 0 1 1.5 0v7.44l2.97-2.97a.75.75 0 0 1 1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="air_quality" data-order="asc">Qualidade do Ar <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M3.47 7.78a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.751.751 0 0 1-.018 1.042.751.751 0 0 1-1.042.018L9 4.81v7.44a.75.75 0 0 1-1.5 0V4.81L4.53 7.78a.75.75 0 0 1-1.06 0Z"></path>
                                            </svg></a></li>
                                    <li><a class="dropdown-item sort-option" href="#" data-column="air_quality" data-order="desc">Qualidade do Ar <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                                <path d="M13.03 8.22a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L3.47 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018l2.97 2.97V3.75a.75.75 0 0 1 1.5 0v7.44l2.97-2.97a.75.75 0 0 1 1.06 0Z"></path>
                                            </svg></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Temperatura (°C)</th>
                                    <th>Humidade (%)</th>
                                    <th>Ruído (dB)</th>
                                    <th>Qualidade do Ar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Linhas da tabela -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('tbody');

            fetch('get_history.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const row = `
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.timestamp}</td>
                            <td>${item.temperature}</td>
                            <td>${item.humidity}</td>
                            <td>${item.noise}</td>
                            <td>${item.air_quality}</td>
                        </tr>`;
                        tableBody.innerHTML += row;
                    });
                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6">Nenhum dado histórico encontrado.</td></tr>';
                    }
                })
                .catch(error => console.error('Erro:', error));
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('tbody');
            let tableData = [];

            fetch('get_history.php')
                .then(response => response.json())
                .then(data => {
                    tableData = data;
                    renderTable(data);
                })
                .catch(error => console.error('Erro:', error));

            function renderTable(data) {
                tableBody.innerHTML = '';
                data.forEach(item => {
                    const row = `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.timestamp}</td>
                    <td>${item.temperature}</td>
                    <td>${item.humidity}</td>
                    <td>${item.noise}</td>
                    <td>${item.air_quality}</td>
                </tr>`;
                    tableBody.innerHTML += row;
                });
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6">Nenhum dado histórico encontrado.</td></tr>';
                }
            }

            function sortTable(column, order) {
                const sortedData = [...tableData].sort((a, b) => {
                    let valA = a[column];
                    let valB = b[column];

                    if (column === 'timestamp') {
                        valA = new Date(valA);
                        valB = new Date(valB);
                    }

                    if (order === 'asc') {
                        return valA > valB ? 1 : -1;
                    } else {
                        return valA < valB ? 1 : -1;
                    }
                });
                renderTable(sortedData);
            }

            document.querySelectorAll('.sort-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const column = this.getAttribute('data-column');
                    const order = this.getAttribute('data-order');
                    sortTable(column, order);
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>