<?php
include 'config.php';


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, location, status, created_at, updated_at FROM sensors";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

if ($stmt->execute() === false) {
    die("Execute failed: " . $stmt->error);
}

$stmt->store_result();

$stmt->bind_result($id, $location, $status, $created_at, $updated_at);
?>
<?php
// retorna os dados do sensor em formato JSON

?>
<div class="table-responsive" id="tableContainer">
    <table class="table table-striped" id="sensorTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Localização</th>
                <th>Status</th>
                <th>Data de Criação</th>
                <th>Data de Atualização</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the results
            while ($stmt->fetch()) {
                echo '<tr>';
                echo "<td data-id='" . $id . "'>" . $id . "</td>";
                echo '<td>' . $location . '</td>';
                echo '<td>';
                echo '<select class="form-select status" data-sensor-id="' . $id . '">';
                echo '<option value="ativo"' . ($status == "ativo" ? ' selected' : '') . '>Ativo</option>';
                echo '<option value="inativo"' . ($status == "inativo" ? ' selected' : '') . '>Inativo</option>';
                echo '</select>';
                echo '</td>';
                echo '<td>' . $created_at . '</td>';
                echo '<td>' . $updated_at . '</td>';
                echo "<td><button class='btn btn-danger btn-sm deleteSensor' data-id='" . $id . "' data-bs-toggle='modal' data-bs-target='#removeSensorModal'>Remover</button></td>";
                echo '</tr>';
            }

            if ($stmt->num_rows === 0) {
                echo '<tr><td colspan="7">Nenhum sensor encontrado.</td></tr>';
            }

            $stmt->close();
            $conn->close();
            ?>
        </tbody>
    </table>
</div>