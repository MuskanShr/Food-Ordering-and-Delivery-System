<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['Pending','Preparing','Out for Delivery','Delivered'];
    if (in_array($status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $oid]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Order status updated.'];
    }
    header('Location: dashboard.php'); exit;
}

$pageTitle = 'Dashboard';
include 'header.php';

$newOrders   = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$preparing   = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Preparing'")->fetchColumn();
$outDelivery = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Out for Delivery'")->fetchColumn();
$revenue     = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status!='Pending'")->fetchColumn();

$orders = $pdo->query("
    SELECT o.*, u.username, u.email,
    GROUP_CONCAT(i.name ORDER BY i.name SEPARATOR ', ') as item_names
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN items i ON oi.item_id = i.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$badgeMap = [
    'Pending'          => 'badge-pending',
    'Preparing'        => 'badge-preparing',
    'Out for Delivery' => 'badge-delivery',
    'Delivered'        => 'badge-delivered',
];
?>

<!-- STATS -->

<?php include 'footer.php'; ?>