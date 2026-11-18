<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$titulo = 'Tutores';
$busca = trim($_GET['busca'] ?? '');

$sql = "SELECT * FROM tutores WHERE nome LIKE ?";
$parametros = ['%' . explode(' ', $busca)[0] . '%'];

if ($busca === '') {
    $sql = "SELECT * FROM tutores";
    $parametros = [];
}

$stmt = $pdo->prepare($sql . " ORDER BY nome");
$stmt->execute($parametros);

$tutores = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';

?>

<h1>Tutores</h1>

<p class="muted">
    Cadastro e consulta de responsáveis pelos animais.
</p>

<div class="actions">
    <a class="btn" href="form.php" onclick="testeErro(event)">
        + Novo tutor
    </a>
</div>

<section class="panel">

    <form method="get">

        <div class="field">
            <label>Buscar tutor</label>
            <input
                type="text"
                name="busca"
                placeholder="Digite o nome"
                value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <button type="submit">
            Buscar
        </button>

    </form>

</section>

<div class="panel table-wrap">

    <table>

        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($tutores as $tutor): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($tutor['nome'], ENT_QUOTES, 'UTF-8') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($tutor['telefone'], ENT_QUOTES, 'UTF-8') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($tutor['email'], ENT_QUOTES, 'UTF-8') ?>
                    </td>

                    <td>
                        <?= $tutor['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>

                    <td>
                        <a href="form.php?id=<?= (int) $tutor['id'] ?>">
                            Editar
                        </a>

                        |

                        <a href="excluir.php?id=<?= (int) $tutor['id'] ?>">
                            Excluir
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
function testeErro(event) {
    event.preventDefault();

    alert("Teste - Erro ao tentar cadastrar tutor. Tente novamente mais tarde.");
}
</script>
