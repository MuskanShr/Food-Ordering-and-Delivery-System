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

<style>
.filter-bar { display:flex; gap:0.5rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.filter-btn {
    padding:0.4rem 1rem; border-radius:20px;
    border:1.5px solid var(--border);
    background:white; font-family:'DM Sans',sans-serif;
    font-size:0.83rem; font-weight:600; cursor:pointer;
    text-decoration:none; color:var(--charcoal); transition:all 0.2s;
}
.filter-btn:hover, .filter-btn.active { background:var(--orange); color:white; border-color:var(--orange); }

.detail-panel {
    background:white; border-radius:14px;
    border:1px solid var(--border); box-shadow:var(--shadow);
    margin-bottom:1.5rem; overflow:hidden;
}
.detail-panel .panel-header {
    background:var(--charcoal); color:white;
    padding:1rem 1.5rem;
    display:flex; align-items:center; justify-content:space-between;
}
.detail-panel .panel-header h3 { font-family:'Playfair Display',serif; font-size:1.1rem; }
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; }
.detail-section { padding:1.5rem; border-right:1px solid var(--border); }
.detail-section:last-child { border-right:none; }
.detail-section h4 { font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; color:var(--gray); margin-bottom:1rem; }
.detail-row { display:flex; gap:0.5rem; margin-bottom:0.5rem; font-size:0.9rem; }
.detail-label { font-weight:600; color:var(--charcoal); min-width:70px; }
.detail-value { color:var(--gray); }

.order-items-table { width:100%; border-collapse:collapse; margin:0; }
.order-items-table th { padding:0.6rem 1.5rem; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.8px; color:var(--gray); background:var(--cream); border-bottom:1px solid var(--border); text-align:left; }
.order-items-table td { padding:0.75rem 1.5rem; border-bottom:1px solid var(--light-gray); font-size:0.88rem; }
.order-items-table tr:last-child td { border-bottom:none; }
.order-total-row { padding:1rem 1.5rem; display:flex; justify-content:space-between; background:var(--cream); font-weight:700; font-size:0.95rem; }
</style>

<!-- FILTER BAR -->
<div class="filter-bar">
    <a href="orders.php" class="filter-btn <?= !$filterStatus ? 'active' : '' ?>">All</a>
    <?php foreach($allowed_statuses as $s): ?>
    <a href="?status=<?= urlencode($s) ?>" class="filter-btn <?= $filterStatus === $s ? 'active' : '' ?>"><?= $s ?></a>
    <?php endforeach; ?>
</div>

<?php if($viewOrder): ?>
<div class="detail-panel">
    <div class="panel-header">
        <h3>Order #<?= $viewOrder['id'] ?></h3>
        <a href="orders.php" class="btn btn-outline btn-sm" style="color:white;border-color:rgba(255,255,255,0.3)">← Back</a>
    </div>
    <div class="detail-grid">
        <div class="detail-section">
            <h4>Customer Details</h4>
            <div class="detail-row"><span class="detail-label">Name:</span><span class="detail-value"><?= htmlspecialchars($viewOrder['name']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value"><?= htmlspecialchars($viewOrder['email']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Phone:</span><span class="detail-value"><?= htmlspecialchars($viewOrder['phone']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Address:</span><span class="detail-value"><?= htmlspecialchars($viewOrder['address']) ?></span></div>
        </div>
        <div class="detail-section">
            <h4>Order Details</h4>
            <div class="detail-row"><span class="detail-label">Date:</span><span class="detail-value"><?= date('Y-m-d', strtotime($viewOrder['created_at'])) ?></span></div>
            <div class="detail-row"><span class="detail-label">Status:</span>
                <span class="badge <?= $badgeMap[$viewOrder['status']] ?? '' ?>"><?= $viewOrder['status'] ?></span>
            </div>
            <div class="detail-row"><span class="detail-label">Total:</span>
                <span class="detail-value" style="font-weight:700;color:var(--orange)">Rs <?= number_format($viewOrder['total'],0) ?></span>
            </div>
            <form method="POST" style="margin-top:1rem;display:flex;gap:0.5rem;align-items:center">
                <input type="hidden" name="order_id" value="<?= $viewOrder['id'] ?>">
                <select name="status" class="form-control" style="padding:0.3rem 0.5rem;font-size:0.82rem;width:160px">
                    <?php foreach($allowed_statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $viewOrder['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
            </form>
        </div>
    </div>
    <div style="border-top:1px solid var(--border)">
        <div style="padding:1rem 1.5rem;font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:var(--gray);background:var(--cream)">Order Items</div>
        <table class="order-items-table">
            <thead><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach($viewItems as $vi): ?>
            <tr>
                <td><?= htmlspecialchars($vi['name']) ?></td>
                <td><?= $vi['quantity'] ?></td>
                <td>Rs <?= number_format($vi['price'],0) ?></td>
                <td>Rs <?= number_format($vi['price'] * $vi['quantity'],0) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="order-total-row"><span>Delivery</span><span>Rs <?= number_format($viewOrder['delivery_charge'],0) ?></span></div>
        <div class="order-total-row" style="color:var(--orange)"><span>Total</span><span>Rs <?= number_format($viewOrder['total'],0) ?></span></div>
    </div>
</div>
<?php endif; ?>

<!-- ORDERS TABLE -->
<div class="card">
    <div class="card-header">
        All Orders <span style="font-size:0.8rem;font-weight:400;color:var(--gray)">(<?= count($orders) ?>)</span>
    </div>
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th><th>Customer</th><th>Items</th>
                    <th>Total</th><th>Address</th><th>Status</th><th>Date</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if(empty($orders)): ?>
                <tr><td colspan="8" style="text-align:center;color:var(--gray);padding:2rem">No orders found</td></tr>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><strong>#<?= $order['id'] ?></strong></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td style="max-width:160px;font-size:0.83rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($order['item_names']) ?></td>
                    <td style="font-weight:700;color:var(--orange)">Rs <?= number_format($order['total'],0) ?></td>
                    <td style="font-size:0.83rem"><?= htmlspecialchars($order['address']) ?></td>
                    <td><span class="badge <?= $badgeMap[$order['status']] ?? '' ?>"><?= $order['status'] ?></span></td>
                    <td style="font-size:0.83rem"><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                    <td>
                        <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                            <a href="?view=<?= $order['id'] ?><?= $filterStatus ? '&status='.urlencode($filterStatus) : '' ?>" class="btn btn-outline btn-sm">👁 View</a>
                            <form method="POST" style="display:inline-flex;gap:0.3rem;align-items:center">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-control" style="padding:0.25rem 0.4rem;font-size:0.76rem;width:130px">
                                    <?php foreach($allowed_statuses as $s): ?>
                                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">✓</button>
                            </form>
                            <a href="?delete=<?= $order['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete order #<?= $order['id'] ?>?')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>