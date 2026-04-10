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
    if (!$phone)   $errors[] = "Phone number is required.";
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




<?php include 'includes/footer.php'; ?>