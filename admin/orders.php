<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$allowed_statuses = ['Pending','Preparing','Out for Delivery','Delivered'];

// UPDATE STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = $_POST['status'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $oid]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Order status updated.'];
    }
    header('Location: orders.php'); exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Order deleted.'];
    header('Location: orders.php'); exit;
}

// VIEW SINGLE ORDER
$viewOrder = null;
$viewItems = [];
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$id]);
    $viewOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($viewOrder) {
        $stmt = $pdo->prepare("SELECT oi.*, i.name FROM order_items oi JOIN items i ON oi.item_id = i.id WHERE oi.order_id = ?");
        $stmt->execute([$id]);
        $viewItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// FILTER
$filterStatus   = $_GET['status'] ?? '';
$statusClause   = '';
$params         = [];
if ($filterStatus && in_array($filterStatus, $allowed_statuses)) {
    $statusClause = "WHERE o.status = ?";
    $params[]     = $filterStatus;
}

$stmt = $pdo->prepare("
    SELECT o.*, u.username,
    GROUP_CONCAT(i.name ORDER BY i.name SEPARATOR ', ') as item_names
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN items i ON oi.item_id = i.id
    $statusClause
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$badgeMap = [
    'Pending'          => 'badge-pending',
    'Preparing'        => 'badge-preparing',
    'Out for Delivery' => 'badge-delivery',
    'Delivered'        => 'badge-delivered',
];

$pageTitle = 'Orders';
include 'header.php';
?>


<?php include 'footer.php'; ?>