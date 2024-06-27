<?php
session_start();
include 'config.php';

// Verificar se o usuário está logado e é um superadmin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
    // Se não estiver logado ou não for superadmin, redirecione para a página de login
    header("Location: login.php");
    exit;
}

// Obter todos os organizadores
$sql = "SELECT id, name, email, is_superadmin FROM organizadores";
$result = $conn->query($sql);

if (!$result) {
    die("Erro ao buscar organizadores: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="http://digitalprogression.pt/img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
                DigitalProgression
            </a>
            <a class="btn btn-outline-light" href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Gestão de Organizadores</h2>
        <div id="message"></div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Superadmin</th>
                    <th>Nova Password</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form class="update-form">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <td><input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>"></td>
                            <td><input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>"></td>
                            <td>
                                <input type="checkbox" name="is_superadmin" value="1" <?php echo $row['is_superadmin'] ? 'checked' : ''; ?>>
                            </td>
                            <td>
                                <input type="password" name="new_password" class="form-control" placeholder="Deixar vazio para manter">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary">Atualizar</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.update-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'edit_accounts.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        let result = JSON.parse(response);
                        $('#message').html('<div class="alert alert-' + (result.status === 'success' ? 'success' : 'danger') + '">' + result.message + '</div>');
                        
                        // Remove the message after 5 seconds
                        setTimeout(function() {
                            $('#message').html('');
                        }, 3000);
                    },
                    error: function() {
                        $('#message').html('<div class="alert alert-danger">Erro ao processar a requisição.</div>');
                        
                        // Remove the message after 5 seconds
                        setTimeout(function() {
                            $('#message').html('');
                        }, 3000);
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>






