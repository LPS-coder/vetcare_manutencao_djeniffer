<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

exigirLogin();

$titulo = 'Painel';

$tutores = (int) $pdo
    ->query("SELECT COUNT(*) FROM tutores WHERE ativo = 1")
    ->fetchColumn();

$animais = (int) $pdo
    ->query("SELECT COUNT(*) FROM animais")
    ->fetchColumn();

$consultas = (int) $pdo
    ->query("SELECT COUNT(*) FROM consultas WHERE status = 'Agendada'")
    ->fetchColumn();

$sql = "
    SELECT
        c.*,
        a.nome AS animal
    FROM consultas c
    INNER JOIN animais a ON a.id = c.animal_id
    ORDER BY c.data_consulta
    LIMIT 5
";

$proximas = $pdo->query($sql)->fetchAll();

include __DIR__ . '/includes/header.php';

?>

<h1>
    Olá, <?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8') ?>
</h1>

<p class="muted">
    Visão geral da clínica veterinária.
</p>

<div class="grid">

    <div class="stat">
        <span>Tutores ativos</span>
        <strong><?= $tutores ?></strong>
    </div>

    <div class="stat">
        <span>Animais cadastrados</span>
        <strong><?= $animais ?></strong>
    </div>

    <div class="stat">
        <span>Consultas agendadas</span>
        <strong><?= $consultas ?></strong>
    </div>

</div>

<section class="panel">

    <h2>Próximas consultas</h2>

    <div class="table-wrap">

        <table>
            <thead>
                <tr>
                    <th>Animal</th>
                    <th>Data</th>
                    <th>Veterinário</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($proximas as $consulta): ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($consulta['animal'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= date('d/m/Y H:i', strtotime($consulta['data_consulta'])) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($consulta['veterinario'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <span class="tag">
                                <?= htmlspecialchars($consulta['status'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

    </div>

</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
