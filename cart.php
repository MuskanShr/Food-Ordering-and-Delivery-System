<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


function fetchItem(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT id, name, price FROM items WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function isAjax(): bool
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}

function cartCount(): int
{
    return (int) array_sum(array_column($_SESSION['cart'], 'qty'));
}

function redirect(string $path): never
{
    header("Location: $path");
    exit;
}

function verifyCsrf(): void
{
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid request.');
    }
}


if (isset($_GET['add'])) {
    $id   = (int) $_GET['add'];
    $item = fetchItem($pdo, $id);

    if ($item) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id'    => $item['id'],
                'name'  => $item['name'],
                'price' => $item['price'],
                'qty'   => 1,
            ];
        }
    }

    if (isAjax()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool) $item, 'cartCount' => cartCount()]);
        exit;
    }

    redirect('/foodbyte/cart.php');
}


if (isset($_POST['update_qty'])) {
    verifyCsrf();

    $id  = (int) $_POST['item_id'];
    $qty = (int) $_POST['qty'];

    if (isset($_SESSION['cart'][$id])) {
        if ($qty > 0) {
            $_SESSION['cart'][$id]['qty'] = $qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }

    redirect('/foodbyte/cart.php');
}


if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    redirect('/foodbyte/cart.php');
}


$cart     = $_SESSION['cart'];
$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
$delivery = $subtotal > 0 ? 100 : 0;
$total    = $subtotal + $delivery;

$pageTitle = 'My Cart';
include 'includes/header.php';
?>



<?php include 'includes/footer.php'; ?>