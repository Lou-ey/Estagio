<?php
include 'config.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT id, nome, descricao FROM espacos";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare falhou: " . $conn->error);
}

if ($stmt->execute() === false) {
    die("Execute falhou: " . $stmt->error);
}

$stmt->bind_result($id, $nome, $descricao);

?>
<div class="table-responsive" id="tableContainer">
    <table class="table table-striped" id="dataTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Espaço</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop através dos resultados
            while ($stmt->fetch()) {
                echo '<tr>';
                echo "<td data-id='" . $id . "'>" . $id . "</td>";
                echo '<td>' . $nome . '</td>';
                echo '<td>' . $descricao . '</td>';
                echo "<td><button class='btn btn-danger btn-sm deleteSpace' data-id='" . $id . "' data-bs-toggle='modal' data-bs-target='#removeSpaceModal'>Remover</button></td>";
                echo '</tr>';
            }

            if ($stmt->num_rows === 0) {
                echo '<tr><td colspan="5">Nenhum espaço encontrado.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$conn->close();
?>