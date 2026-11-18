<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

$tutor = [
    'nome' => '',
    'telefone' => '',
    'email' => '',
    'endereco' => '',
    'ativo' => 1
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM tutores WHERE id = ?");
    $stmt->execute([$id]);

    $tutor = $stmt->fetch() ?: $tutor;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $ativo = (int) ($_POST['ativo'] ?? 1);

    if ($nome === '') {

        $erro = 'Informe o nome.';

    } else {

        if ($id) {

            $sql = "
                UPDATE tutores
                SET nome = ?, telefone = ?, email = ?, endereco = ?, ativo = ?
                WHERE id = ?
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nome,
                $telefone,
                $email,
                $endereco,
                $ativo,
                $id
            ]);

        } else {

            $sql = "
                INSERT INTO tutores (nome, telefone, email, endereco)
                VALUES (?, ?, ?, ?)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nome,
                $telefone,
                $email,
                $endereco
            ]);

        }

        header('Location: index.php');
        exit;

    }
}

$titulo = $id ? 'Editar tutor' : 'Novo tutor';

include __DIR__ . '/../includes/header.php';

?>

<h1><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>

<section class="panel">

    <?php if ($erro): ?>

        <div class="alert">
            <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="field">
            <label>Nome *</label>
            <input
                type="text"
                name="nome"
                value="<?= htmlspecialchars($tutor['nome'], ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div class="field">
            <label>Telefone</label>
            <input
                type="text"
                name="telefone"
                value="<?= htmlspecialchars($tutor['telefone'], ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="field">
            <label>E-mail</label>
            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars($tutor['email'], ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="field">
            <label>Endereço</label>
            <input
                type="text"
                name="endereco"
                value="<?= htmlspecialchars($tutor['endereco'], ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <?php if ($id): ?>

            <div class="field">

                <label>Situação</label>

                <select name="ativo">

                    <option value="1" <?= $tutor['ativo'] ? 'selected' : '' ?>>
                        Ativo
                    </option>

                    <option value="0" <?= !$tutor['ativo'] ? 'selected' : '' ?>>
                        Inativo
                    </option>

                </select>

            </div>

        <?php endif; ?>

        <button type="submit">
            Salvar
        </button>

        <a class="btn secondary" href="index.php">
            Cancelar
        </a>

    </form>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
