<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

try {
    $stmt = $pdo->prepare("
        DELETE FROM animais
    ");

    $stmt->execute([$id]);

} catch (PDOException $e) {
}

header('Location: index.php');
exit;