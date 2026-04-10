<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$pageTitle = 'Search';

// AJAX
if (isset($_GET['q'], $_GET['ajax'])) {
    $q    = '%' . trim($_GET['q']) . '%';
    $stmt = $pdo->prepare("SELECT i.*, c.name as cat_name FROM items i JOIN categories c ON i.category_id = c.id WHERE i.name LIKE ? OR i.description LIKE ? ORDER BY i.name LIMIT 20");
    $stmt->execute([$q, $q]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$query = trim($_GET['q'] ?? '');
$items = [];
if ($query) {
    $q    = '%' . $query . '%';
    $stmt = $pdo->prepare("SELECT i.*, c.name as cat_name FROM items i JOIN categories c ON i.category_id = c.id WHERE i.name LIKE ? OR i.description LIKE ? ORDER BY i.name");
    $stmt->execute([$q, $q]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>



<?php include 'includes/footer.php'; ?>