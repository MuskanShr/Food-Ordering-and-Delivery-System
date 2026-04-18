<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: /foodbyte/cart.php');
    exit;
}

// Re-validate prices from DB so users can't tamper with session prices
$ids  = array_keys($cart);
$stmt = $pdo->prepare("SELECT id, price FROM items WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")");
$stmt->execute($ids);
$dbPrices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => price]

foreach ($cart as $id => $item) {
    if (isset($dbPrices[$id])) {
        $_SESSION['cart'][$id]['price'] = $dbPrices[$id];
    }
}
$cart = $_SESSION['cart'];

// Totals
$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
$delivery = 100;
$total    = $subtotal + $delivery;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!$name)    $errors[] = "Name is required.";
    if (!$address) $errors[] = "Address is required.";

    // Phone validation
if (!$phone) {
    $errors[] = "Phone number is required.";
} elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
    $errors[] = "Enter a valid phone number (digits only, 7–15 characters).";
}

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Insert order
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, name, phone, address, total, delivery_charge)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $name, $phone, $address, $total, $delivery]);
            $orderId = (int) $pdo->lastInsertId();

            // Insert each item
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, item_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($cart as $item) {
                $stmt->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
            }

            $pdo->commit();

            $_SESSION['cart']  = [];
            $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Order placed and will arrive shortly!'];
            header('Location: /foodbyte/index.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Something went wrong. Please try again.";
            error_log('Checkout error: ' . $e->getMessage());
        }
    }
}

$pageTitle = 'Checkout';
include 'includes/header.php';
?>


<style>
.checkout-container {
    max-width:600px; margin:3rem auto; padding:0 2rem;
}
.checkout-box {
    background:var(--warm-white);
    border-radius:16px;
    border:1px solid var(--border);
    overflow:hidden;
    box-shadow:var(--shadow);
}
.checkout-box h2 {
    padding:1.3rem 1.8rem;
    font-family:'Playfair Display',serif;
    font-size:1.3rem; font-weight:800;
    border-bottom:1px solid var(--border);
    background:var(--cream);
}
.checkout-form { padding:1.8rem; }
.form-group { margin-bottom:1.3rem; }
.form-group label {
    display:block; font-size:0.85rem; font-weight:600;
    margin-bottom:0.4rem; color:var(--charcoal);
}
.form-group input, .form-group textarea {
    width:100%;
    padding:0.7rem 1rem;
    border:1.5px solid var(--border);
    border-radius:10px;
    font-family:'DM Sans',sans-serif;
    font-size:0.92rem;
    background:white;
    transition:border 0.2s;
    outline:none;
}
.form-group input:focus, .form-group textarea:focus {
    border-color:var(--orange);
}
.form-group textarea { resize:vertical; min-height:80px; }

.payment-section {
    border-top:1px solid var(--border);
    padding:1.5rem 1.8rem;
}
.payment-section h3 {
    font-family:'Playfair Display',serif;
    font-size:1.1rem; font-weight:800;
    margin-bottom:1rem;
}
.payment-row {
    display:flex; justify-content:space-between;
    padding:0.4rem 0; font-size:0.92rem;
    color:var(--gray);
}
.payment-row.total {
    font-weight:800; font-size:1.05rem;
    color:var(--charcoal);
    padding-top:0.8rem;
    margin-top:0.5rem;
    border-top:1px solid var(--border);
}

.errors {
    background:#FFEBEE; border:1px solid #FFCDD2;
    border-radius:10px; padding:1rem 1.2rem;
    margin-bottom:1rem;
    color:#C62828; font-size:0.88rem;
}
.errors ul { margin-left:1.2rem; }

.place-btn {
    width:100%;
    background:var(--orange); color:#fff;
    border:none; border-radius:10px;
    padding:0.9rem;
    font-size:0.95rem; font-weight:700;
    cursor:pointer; transition:all 0.2s;
    font-family:'DM Sans',sans-serif;
    margin-top:1.2rem;
}
.place-btn:hover { background:var(--orange-dark); }
</style>

<div class="checkout-container">
    <div class="checkout-box">
        <h2>Delivery Details</h2>
        <div class="checkout-form">
            <?php if(!empty($errors)): ?>
            <div class="errors">
                <ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name']??$_SESSION['username']??'') ?>" required>
                </div>
                <div class="form-group">
    <label>Phone Number *</label>
    <input type="tel" name="phone"
           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
           pattern="^\+?[0-9]{7,15}$"
           title="Digits only, 7–15 characters. You may start with +"
           required>
</div>
              
                <div class="form-group">
                    <label>Address *</label>
                    <textarea name="address" required><?= htmlspecialchars($_POST['address']??'') ?></textarea>
                </div>

                <div class="payment-section">
                    <h3>Payment Details</h3>
                    <div class="payment-row"><span>Subtotal</span><span>Rs <?= number_format($subtotal,0) ?></span></div>
                    <div class="payment-row"><span>Delivery Charge</span><span>Rs <?= $delivery ?></span></div>
                    <div class="payment-row total"><span>Total Amount</span><span>Rs <?= number_format($total,0) ?></span></div>

                    
                </div>

                <button type="submit" class="place-btn">Place Order →</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>