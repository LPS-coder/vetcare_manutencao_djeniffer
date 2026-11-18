<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$titulo = 'Consultas';
$status = $_GET['status'] ?? '';

$sql = "
    SELECT c.*, a.nome AS animal
    FROM consultas c
    JOIN animais a ON a.id = c.animal_id
";

$consultas = $pdo->query($sql . " ORDER BY c.data_consulta DESC")
    ->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Consultas</h1>

<p class="muted">Agenda e histórico de atendimentos.</p>

<div class="actions">
    <a class="btn" href="form.php">+ Nova consulta</a>
</div>

<section class="panel">

    <form method="get">

        <div class="field">
            <label>Filtrar por status</label>

            <select name="status">
                <option value="">Todos</option>

                <?php foreach (['Agendada', 'Realizada', 'Cancelada'] as $opcao): ?>
                    <option
                        value="<?= $opcao ?>"
                        <?= $status === $opcao ? 'selected' : '' ?>
                    >
                        <?= $opcao ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button>Filtrar</button>

    </form>

</section>

<div class="panel table-wrap">

    <table>
        <tr>
            <th>Animal</th>
            <th>Data</th>
            <th>Veterinário</th>
            <th>Motivo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($consultas as $consulta): ?>
            <tr>
                <td><?= htmlspecialchars($consulta['animal']) ?></td>

                <td>
                    <?= date('d/m/Y H:i', strtotime($consulta['data_consulta'])) ?>
                </td>

                <td><?= htmlspecialchars($consulta['veterinario']) ?></td>
                <td><?= htmlspecialchars($consulta['motivo']) ?></td>
                <td><?= htmlspecialchars($consulta['status']) ?></td>

                <td>
                    <a href="form.php?id=<?= $consulta['id'] ?>">Editar</a>
                    |
                    <a href="excluir.php?id=<?= $consulta['id'] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>