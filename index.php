<?php
$mysqli = new mysqli("localhost", "root", "", "tarefas");

if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}

// Adicionar nova tarefa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tarefa'])) {
    $tarefa = $mysqli->real_escape_string($_POST['tarefa']);
    $mysqli->query("INSERT INTO lista (tarefa) VALUES ('$tarefa')");
}

// Concluir tarefa
if (isset($_POST['concluir'])) {
    $id = $_POST['concluir'];
    $mysqli->query("UPDATE lista SET concluida = 1 WHERE id = $id");
}

// Remover tarefa
if (isset($_POST['remover'])) {
    $id = $_POST['remover'];
    $mysqli->query("DELETE FROM lista WHERE id = $id");
}

// Editar tarefa
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nova_tarefa = $mysqli->real_escape_string($_POST['nova_tarefa']);
    $mysqli->query("UPDATE lista SET tarefa = '$nova_tarefa' WHERE id = $id");
}

// Reverter tarefa para pendente
if (isset($_POST['reverter'])) {
    $id = $_POST['reverter'];
    $mysqli->query("UPDATE lista SET concluida = 0 WHERE id = $id");
}

// Obter todas as tarefas
$result = $mysqli->query("SELECT * FROM lista");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Lista de Tarefas</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <h1>Lista de Tarefas</h1>
    <form method="post">
        <input type="text" name="tarefa" placeholder="Nova tarefa" required>
        <button class="add" type="submit">Adicionar</button>
    </form>

    <h2>Tarefas Pendentes</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php if (!$row['concluida']): ?>
                <li>
                    <?= htmlspecialchars($row['tarefa']) ?>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="concluir" value="<?= $row['id'] ?>">
                        <span class="material-symbols-outlined" style="color:#1abc9c;">
check_circle
</span></button>
                        <button type="submit" name="remover" value="<?= $row['id'] ?>">
                        <span class="material-symbols-outlined" style="color:#e74c3c;">
cancel
</span>
</button>
                        <button type="button" onclick="document.getElementById('editForm<?= $row['id'] ?>').style.display='block'">
                        <span class="material-symbols-outlined" style="color:#3498db;">
edit
</span>
</button>
                    </form>

                    <!-- Formulário de edição -->
                    <div id="editForm<?= $row['id'] ?>" style="display:none;">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="text" name="nova_tarefa" value="<?= htmlspecialchars($row['tarefa']) ?>" required>
                            <button type="submit" name="editar">Salvar</button>
                            <button type="button" onclick="document.getElementById('editForm<?= $row['id'] ?>').style.display='none'">Cancelar</button>
                        </form>
                    </div>
                </li>
            <?php endif; ?>
        <?php endwhile; ?>
    </ul>

    <h2>Tarefas Concluídas</h2>
    <ul>
        <?php
        $result_concluidas = $mysqli->query("SELECT * FROM lista WHERE concluida = 1");
        while ($row = $result_concluidas->fetch_assoc()): ?>
            <li>
                <?= htmlspecialchars($row['tarefa']) ?>
                <form method="post" style="display:inline;">
                    <button type="submit" name="reverter" value="<?= $row['id'] ?>"><span class="material-symbols-outlined"style="color:#e74c3c;">
indeterminate_check_box
</span></button>
                    <button type="submit" name="remover" value="<?= $row['id'] ?>"><span class="material-symbols-outlined"style="color:#e74c3c;">
cancel
</span>
</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

</body>
</html>

<?php
$mysqli->close();
?>
