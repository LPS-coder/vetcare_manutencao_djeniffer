<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$titulo = 'Animais';

$animais = $pdo->query("
    SELECT a.*, t.nome AS tutor
    FROM animais a
    JOIN tutores t ON t.id = a.tutor_id
    ORDER BY a.nome
")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Animais</h1>

<p class="muted">Pacientes cadastrados na clínica.</p>

<div class="actions">
    <a class="btn" href="form.php">+ Novo animal</a>
</div>

<div class="panel table-wrap">

    <table>
        <tr>
            <th>Nome</th>
            <th>Espécie</th>
            <th>Raça</th>
            <th>Tutor</th>
            <th>Nascimento</th>
            <th>Idade</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($animais as $animal): ?>
            <?php
                $idade = $animal['nascimento']
                    ? date('Y') - (int) date('Y', strtotime($animal['nascimento']))
                    : 0;
            ?>

            <tr>
                <td><?= htmlspecialchars($animal['nome']) ?></td>
                <td><?= htmlspecialchars($animal['especie']) ?></td>
                <td><?= htmlspecialchars($animal['raca']) ?></td>
                <td><?= htmlspecialchars($animal['tutor']) ?></td>

                <td>
                    <?= $animal['nascimento']
                        ? date('d/m/Y', strtotime($animal['nascimento']))
                        : '-' ?>
                </td>

                <td><?= $idade ?> ano(s)</td>

                <td>
                    <a href="form.php?id=<?= $animal['id'] ?>">Editar</a>
                    |
                    <a href="excluir.php?id=<?= $animal['id'] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>