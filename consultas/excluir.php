<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM consultas WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;