<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$uid = (int) $_SESSION['user_id'];

// User details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

// Order stats
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total_orders,
           COALESCE(SUM(total), 0) as total_spent,
           MAX(created_at) as last_order
    FROM orders WHERE user_id = ?
");
$stmt->execute([$uid]);
$stats = $stmt->fetch();

// Recent orders
$stmt = $pdo->prepare("
    SELECT id, total, status, created_at
    FROM orders WHERE user_id = ?
    ORDER BY created_at DESC LIMIT 5
");
$stmt->execute([$uid]);
$recentOrders = $stmt->fetchAll();

$pageTitle = 'My Profile';
include 'includes/header.php';
?>


<?php include 'includes/footer.php'; ?>