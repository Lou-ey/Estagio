<?php
include 'config.php';
include 'fetch_conditions.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT id, nome FROM espacos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->bind_result($id, $nome);

$espacos = [];
while ($stmt->fetch()) {
    $espacos[] = ['id' => $id, 'nome' => $nome];
}

$sql = "SELECT id, planta_path FROM plantas";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->bind_result($id, $planta_path);

if ($stmt->fetch()) {
    $planta_path = $planta_path;
} else {
    $planta_path = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-light">
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

            <div class="d-flex">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownAlertas" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg> <span class="badge bg-danger" id="alertCount">0</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownAlertas" id="alertDropdown"></ul>
                </div>

                <!-- Div para exibir alertas -->
                <div class="position-fixed" style="top: 70px; right: 180px; transform: translateX(50%); z-index: 1050; max-width: 400px;">
                    <div id="alertMessages" aria-live="polite" aria-atomic="true"></div>
                </div>

                <a class="btn btn-outline-light mx-2" href="logout.php">Log out</a>
                <a class="btn btn-outline-light" href="http://digitalprogression.pt/" target="_blank">Saber Mais</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php
        if (isset($_SESSION['id'])) {
            echo "<h1 class='mb-5'>Bem-vindo, " . $_SESSION['name'] . "!</h1>";
        }
        ?>
        <!-- Div para exibir alertas -->
        <div class="position-fixed" style="top: 70px; right: 180px; transform: translateX(50%); z-index: 1050; max-width: 400px;">
            <div id="alertMessages" aria-live="polite" aria-atomic="true"></div>
        </div>
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" id="spaceTab" data-bs-toggle="tab" href="#spaces" role="tab">Adicionar Espaços</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sensorTab" data-bs-toggle="tab" href="#sensors" role="tab">Adicionar
                    Sensores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="alertTab" data-bs-toggle="tab" href="#alerts" role="tab">Configuração de
                    Alertas</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Alert Configuration -->
            <div class="tab-pane fade" id="alerts" role="tabpanel">
                <div id="conditionMessageArea" class="mb-3" role="alert"></div>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Configuração de Alertas
                    </div>

                    <div class="card-body">
                        <form id="alertForm">
                            <?php
                            include 'config.php';

                            $sql = "SELECT id, nome FROM espacos";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                            ?>
                                <div class="mb-3">
                                    <label for="spaceSelect" class="form-label">Espaço</label>
                                    <select class="form-control" id="spaceSelect" name="space_id">
                                        <option value="">Selecione um Espaço</option>
                                        <?php
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                                        }
                                        $space_id = $row['id'];
                                        ?>
                                    </select>
                                </div>
                            <?php
                            } else {
                                echo "<div class='alert alert-warning' role='alert'><img src='./img/icons/warning.png' alt='warning icon' class='me-2'>Não existem espaços disponíveis.</div>";
                            }
                            ?>
                            <?php
                            $sql = "SELECT id, username, location FROM sensors WHERE status = 'ativo'";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                            ?>
                                <div class="mb-3" id="sensorSelectDiv" style="display: none;">
                                    <label for="sensorSelect" class="form-label">Sensor</label>
                                    <select class="form-control" id="sensorSelect" name="sensor_id">
                                        <option value="">Selecione um Sensor</option>
                                        <?php
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php
                            } else {
                                echo "<div class='alert alert-warning mt-3' role='alert'><img src='./img/icons/warning.png' alt='warning icon' class='me-2'>Não existem sensores disponíveis ou ativos.</div>";
                            }
                            ?>

                            <div class="row">
                                <div class="col-md mb-3" id="sensorTypeDiv" style="display: none;">
                                    <label for="sensorType" class="form-label">Tipo de Sensor</label>
                                    <select class="form-control" id="typesSelect" name="sensor_type">
                                        <option value="Temperatura">Temperatura</option>
                                        <option value="Humidade">Humidade</option>
                                        <option value="Qualidade do Ar">Qualidade do Ar</option>
                                        <option value="Ruído">Ruído</option>
                                    </select>
                                </div>
                                <div class="col-md mb-3" id="operatorValueDiv" style="display: none;">
                                    <label for="operatorSelect" class="form-label">Operador</label>
                                    <select class="form-control" id="operatorSelect" name="operator">
                                        <option value=">">Maior que</option>
                                        <option value="<">Menor que</option>
                                        <option value="=">Igual a</option>
                                        <option value=">=">Maior ou igual a</option>
                                        <option value="<=">Menor ou igual a</option>
                                    </select>
                                </div>
                                <div class="col-md mb-3" id="valueInputDiv" style="display: none;">
                                    <label for="valueInput" class="form-label">Valor</label>
                                    <input type="text" class="form-control" id="valueInput" name="value" placeholder="Digite o valor">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="addCondition">Adicionar Condição</button>
                        </form>
                    </div>
                </div>
                <!-- Section for existing conditions -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Condições Existentes
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="existingConditionsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sensor ID</th>
                                    <th>Tipo de Condição</th>
                                    <th>Valor</th>
                                    <th>Data de Criação</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Existing conditions will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal de Edição -->
                <div class="modal fade" id="editConditionModal" tabindex="-1" aria-labelledby="editConditionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editConditionModalLabel">Editar Condição</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editConditionForm">
                                    <div class="mb-3">
                                        <label for="editId" class="form-label">ID</label>
                                        <input type="text" class="form-control" id="editId" name="id" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editOperator" class="form-label">Operador</label>
                                        <select class="form-control" id="editOperator" name="operator">
                                            <option value=">">Maior que</option>
                                            <option value="<">Menor que</option>
                                            <option value="=">Igual a</option>
                                            <option value=">=">Maior ou igual a</option>
                                            <option value="<=">Menor ou igual a</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editValue" class="form-label">Valor</label>
                                        <input type="text" class="form-control" id="editValue" name="value">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="saveConditionButton">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sensor Configuration -->
            <div class="tab-pane fade" id="sensors" role="tabpanel">
                <div id="sensorMessageArea" class="mb-3" role="alert"></div>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Lista de Sensores
                    </div>
                    <div class="card-body">
                        <!-- Botão para abrir o modal -->
                        <button type="button" class="btn btn-primary justify-content-center align-items-center mb-2" data-bs-toggle="modal" data-bs-target="#addSensorModal">
                            <img src="./img/icons/add_icon.png" alt="">
                        </button>
                        <?php include 'get_sensors.php'; ?>
                    </div>
                </div>
            </div>

            <!-- Modal para adicionar sensor -->
            <div class="modal fade" id="addSensorModal" tabindex="-1" aria-labelledby="addSensorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addSensorModalLabel">Adicionar Sensor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="sensorForm" action="add_sensor.php" method="post">

                                <div class="mb-3">
                                    <label for="location">Localização</label>
                                    <select class="form-control" id="location" name="location">
                                        <?php
                                        if (count($espacos) == 0) {
                                            echo "<option value=''>Nenhum espaço disponível</option>";
                                        } else {
                                            echo "<option value=''>Selecione um Espaço</option>";
                                            foreach ($espacos as $espaco) : ?>
                                                <option value="<?= $espaco['id'] ?>"><?= $espaco['nome'] ?></option>
                                        <?php endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <button type="submit" class="btn btn-primary">Adicionar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para remoção do sensor -->
            <div class="modal fade" id="removeSensorModal" tabindex="-1" aria-labelledby="removeSensorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="removeSensorModalLabel">Remover Sensor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja remover este sensor?</p>
                            <form id="removeSensorForm" action="remove_sensor.php" method="post">
                                <input type="hidden" id="sensor_id" name="sensor_id">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="confirmSensorDelete" data-bs-dismiss="modal">Remover</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show active" id="spaces" role="tabpanel">
                <div id="spaceMessageArea" class="mb-3"></div>

                <!-- Section to add spaces -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Lista de Espaços
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addSpaceModal">
                            <img src="./img/icons/add_icon.png" alt="">
                        </button>
                        <?php include 'get_spaces.php'; ?>
                    </div>
                </div>

                <!-- Section to upload plants and add polygons -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        Upload de Plantas e Adição de Polígonos
                    </div>
                    <div class="card-body">
                        <div id="plantMessageArea" class="mb-3"></div>
                        <!-- Botão para abrir o modal de upload da planta -->
                        <button type="button" class="btn btn-secondary mb-2" id="addPlantButton" data-bs-toggle="modal" data-bs-target="#uploadPlantaModal">
                            Adicionar Planta
                        </button>
                        <button type="button" class="btn btn-danger mb-2" id="removePlantButton" data-bs-toggle="modal" data-bs-target="#removePlantModal">
                            Remover Planta
                        </button>
                        <!-- Dropdown com as plantas disponíveis -->
                        <div class="mb-3">
                            <label for="plantaSelect" class="form-label">Planta</label>
                            <select class="form-control" id="plantaSelect" name="planta_id"></select>
                            <div class="d-flex justify-content-center" id="canvasContainer" style="display: block;">
                                <canvas class="" id="canvas" width="500" height="500">
                                    <img class="img-fluid" id="plantImage" src="" alt="">
                                </canvas>
                            </div>

                            <!-- Tabela para mostrar informações do espaço -->
                            <table class="table mt-3" id="spaceInfoTable">
                                <thead>
                                    <tr>
                                        <th>ID do Espaço</th>
                                        <th>Nome do Espaço</th>
                                        <th>IDs dos Sensores</th>
                                        <th>Polígonos</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Linhas serão adicionadas dinamicamente aqui -->
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary mt-3" id="addPolygon">Adicionar Polígono</button>
                                <button class="btn btn-primary mt-3 mx-2" id="lockPolygon">Travar Polígono</button>
                                <button class="btn btn-primary mt-3" id="savePolygon">Salvar Polígono</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para remover a planta -->
                    <div class="modal fade" id="removePlantModal" tabindex="-1" aria-labelledby="removePlantModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="removePlantModalLabel">Remover Planta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem certeza que deseja remover esta planta?</p>
                                    <form id="removePlantForm" action="remove_plant.php" method="post">
                                        <input type="hidden" id="plantaIdInput" name="planta_id">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="confirmPlantDelete" data-bs-dismiss="modal" data-bs-target="#removePlantModal">Remover</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para adicionar espaço -->
                    <div class="modal fade" id="addSpaceModal" tabindex="-1" aria-labelledby="addSpaceModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addSpaceModalLabel">Adicionar Espaço</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="spaceForm" action="add_space.php" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="space_name">Nome do Espaço</label>
                                            <input type="text" class="form-control" id="space_name" name="space_name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="space_description">Descrição</label>
                                            <textarea class="form-control" id="space_description" name="space_description" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Adicionar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for associating space to polygon -->
                    <div class="modal fade" id="polygonModal" tabindex="-1" aria-labelledby="polygonModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="polygonModalLabel">Associar Espaço ao Polígono</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="polygonAssociationForm">
                                        <div class="mb-3">
                                            <label for="space_id" class="form-label">ID do Espaço:</label>
                                            <select class="form-control" id="space_id" name="space_id">
                                                <?php foreach ($espacos as $espaco) : ?>
                                                    <option value="<?= $espaco['id'] ?>"><?= $espaco['id'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="space_name" class="form-label">Nome do Espaço:</label>
                                            <select class="form-control" id="space_name1" name="space_name">
                                                <?php foreach ($espacos as $espaco) : ?>
                                                    <option value="<?= $espaco['nome'] ?>"><?= $espaco['nome'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sensor_ids" class="form-label">IDs do Sensor (separados por vírgula):</label>
                                            <input class="form-control" type="text" id="sensor_ids" name="sensor_ids">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    <button type="button" class="btn btn-primary" id="saveSpaceAssociation">Salvar Associação</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para upload da planta -->
                    <div class="modal fade" id="uploadPlantaModal" tabindex="-1" aria-labelledby="uploadPlantaModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadPlantaModalLabel">Adicionar Planta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="uploadPlantaForm" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="plantaInput" class="form-label">Escolha a planta:</label>
                                            <input type="file" class="form-control" id="plantaInput" name="plantaInput">
                                            <input type="hidden" id="espacoIdInput" name="espaco_id">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal de Remoção do espaco -->
                    <div class="modal fade" id="removeSpaceModal" tabindex="-1" aria-labelledby="removeSpaceModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="removeSpaceModalLabel">Confirmar Exclusão</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem certeza que deseja excluir este espaço?</p>
                                    <form id="removeSpaceForm" action="remove_space.php" method="post">
                                        <input type="hidden" id="space_id" name="space_id">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="confirmSpaceDelete">Remover</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Ao clicar no botão "Adicionar Planta"
                    var addPlantButtons = document.querySelectorAll(".addPlantButton");
                    addPlantButtons.forEach(function(button) {
                        button.addEventListener("click", function() {
                            // Obtém o ID do espaço associado ao botão
                            var espacoId = button.getAttribute("data-id");
                            // Define o ID do espaço no campo oculto do formulário
                            document.getElementById("espacoIdInput").value = espacoId;
                        });
                    });

                    // Ao enviar o formulário de upload de planta
                    var uploadForm = document.getElementById("uploadPlantaForm");
                    uploadForm.addEventListener("submit", function(event) {
                        event.preventDefault(); // Impede o envio padrão do formulário
                        var messageArea = document.getElementById("plantMessageArea");
                        // Obtém os dados do formulário
                        var formData = new FormData(uploadForm);

                        // Envia os dados do formulário usando AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "upload_planta.php", true);
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                // Mostra a resposta do servidor (por exemplo, sucesso ou erro)
                                showMessages("Planta adicionada com sucesso.", "success");
                                setTimeout(function() {
                                    messageArea.innerHTML = "";
                                }, 3000);
                                // Atualiza a página para refletir as mudanças
                                window.location.reload();
                            } else {
                                // Em caso de erro, mostra uma mensagem de erro
                                alert("Erro ao enviar o formulário.");
                            }
                        };
                        xhr.send(formData);
                    });

                    // Quando o modal é exibido, define o ID da planta no campo oculto
                    $('#removePlantModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget); // Botão que acionou o modal
                        var plantaSelect = document.getElementById('plantaSelect');
                        var plantId = plantaSelect.options[plantaSelect.selectedIndex].value;
                        var modal = $(this);
                        modal.find('#plantaIdInput').val(plantId);
                    });

                    // Ao selecionar uma planta no seletor, atualize o canvas
                    var plantaSelect = document.getElementById('plantaSelect');
                    plantaSelect.addEventListener('change', updateCanvas);

                    var removePlantButton = document.getElementById("confirmPlantDelete");
                    removePlantButton.addEventListener("click", function() {
                        var messageArea = document.getElementById("plantMessageArea");
                        var formData = new FormData(document.getElementById("removePlantForm"));

                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "remove_planta.php", true);
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                var response = JSON.parse(xhr.responseText);
                                if (response.status === 'success') {
                                    showMessages(response.message, 'success');

                                    // Remove a planta da lista de seleção
                                    document.querySelector(`[value="${formData.get('planta_id')}"]`).remove();

                                    updateCanvas(); // Atualizar o canvas com a primeira planta após a remoção
                                    window.location.reload(); // Atualizar a página para refletir as mudanças
                                    setTimeout(function() {
                                        messageArea.innerHTML = "";
                                        $('#removePlantModal').modal('hide');
                                    }, 3000);
                                } else {
                                    showMessages(response.message, 'danger');
                                }
                            } else {
                                showMessages('Erro ao remover a planta.', 'danger');
                            }
                        };
                        xhr.send(formData);
                    });

                    function updateCanvas() {
                        var plantaSelect = document.getElementById('plantaSelect');
                        var selectedPlantId = plantaSelect.options[plantaSelect.selectedIndex].value;

                        // Obter a imagem correspondente ao ID da planta selecionada
                        var imgSrc = ''; // Defina a imagem correspondente com base no ID da planta

                        // Atualizar o canvas com a imagem correspondente
                        var canvas = document.getElementById('canvas');
                        var ctx = canvas.getContext('2d');
                        var img = new Image();
                        img.onload = function() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        };
                        img.src = imgSrc;
                    }

                    function showMessages(message, type) {
                        var messageArea = document.getElementById("plantMessageArea");
                        messageArea.innerHTML = "<div class='alert alert-" + type + "'>" + message + "</div>";
                    }

                });
            </script>

            <script src="./scripts/add_sensor.js"></script>
            <script src="./scripts/remove_sensor.js"></script>
            <script src="./scripts/add_space.js"></script>
            <script src="./scripts/remove_space.js"></script>
            <script src="./scripts/add_condition.js"></script>
            <script src="./scripts/polygon.js"></script>
            <script src="./scripts/alerts.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>