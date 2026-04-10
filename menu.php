<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$pageTitle = 'Menu';

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();

// Active category
$catId = isset($_GET['cat']) ? (int)$_GET['cat'] : ($categories[0]['id'] ?? 0);

// Get items for selected category
$stmt = $pdo->prepare("SELECT * FROM items WHERE category_id = ? ORDER BY id");
$stmt->execute([$catId]);
$items = $stmt->fetchAll();

include 'includes/header.php';
?>


<?php include 'includes/footer.php'; ?>