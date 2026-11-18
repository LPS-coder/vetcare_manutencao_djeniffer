<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

$consulta = [
    'animal_id' => '',
    'data_consulta' => '',
    'veterinario' => '',
    'motivo' => '',
    'status' => 'Agendada',
    'observacoes' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM consultas WHERE id = ?");
    $stmt->execute([$id]);

    $consulta = $stmt->fetch() ?: $consulta;
}

$animais = $pdo->query("
    SELECT id, nome
    FROM animais
    ORDER BY nome
")->fetchAll();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data_consulta'] ?? '';

    $dataFormatada = str_replace('T', ' ', $data)
        . (strlen($data) === 16 ? ':00' : '');

    if (trim($_POST['veterinario'] ?? '') === '') {
        $erro = 'Informe o veterinário.';
    } else {
        $dados = [
            (int) $_POST['animal_id'],
            $dataFormatada,
            $_POST['veterinario'],
            $_POST['motivo'],
            $_POST['status'],
            $_POST['observacoes']
        ];

        if ($id) {
            $stmt = $pdo->prepare("
                UPDATE consultas
                SET animal_id = ?, data_consulta = ?, veterinario = ?,
                    motivo = ?, status = ?, observacoes = ?
                WHERE id = ?
            ");

            $stmt->execute([...$dados, $id]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO consultas (
                    animal_id, data_consulta, veterinario,
                    motivo, status, observacoes
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute($dados);
        }

        header('Location: index.php');
        exit;
    }
}

$titulo = $id ? 'Editar consulta' : 'Nova consulta';

include __DIR__ . '/../includes/header.php';
?>

<h1><?= $titulo ?></h1>

<section class="panel">

    <?php if ($erro): ?>
        <div class="alert"><?= $erro ?></div>
    <?php endif; ?>

    <form method="post">

        <div class="field">
            <label>Animal</label>

            <select name="animal_id" required>
                <option value="">Selecione</option>

                <?php foreach ($animais as $animal): ?>
                    <option
                        value="<?= $animal['id'] ?>"
                        <?= $consulta['animal_id'] == $animal['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($animal['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Data e horário</label>

            <input
                type="datetime-local"
                name="data_consulta"
                value="<?= $consulta['data_consulta'] ? date('Y-m-d\TH:i', strtotime($consulta['data_consulta'])) : '' ?>"
                required
            >
        </div>

        <div class="field">
            <label>Veterinário *</label>

            <input
                name="veterinario"
                value="<?= htmlspecialchars($consulta['veterinario']) ?>"
                required
            >
        </div>

        <div class="field">
            <label>Motivo</label>

            <input
                name="motivo"
                value="<?= htmlspecialchars($consulta['motivo']) ?>"
            >
        </div>

        <div class="field">
            <label>Status</label>

            <select name="status">
                <?php foreach (['Agendada', 'Realizada', 'Cancelada'] as $opcao): ?>
                    <option <?= $consulta['status'] === $opcao ? 'selected' : '' ?>>
                        <?= $opcao ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Observações</label>

            <textarea name="observacoes"><?= htmlspecialchars($consulta['observacoes']) ?></textarea>
        </div>

        <button>Salvar</button>
        <a class="btn secondary" href="index.php">Cancelar</a>

    </form>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>