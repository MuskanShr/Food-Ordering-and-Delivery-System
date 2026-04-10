<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);

// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name   = trim($_POST['name'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $cat_id = (int)($_POST['category_id'] ?? 0);
    $price  = (float)($_POST['price'] ?? 0);
    $image  = null;

    if ($name && $cat_id && $price > 0) {
        if (!empty($_FILES['image']['name'])) {
            $ext     = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array(strtolower($ext), $allowed)) {
                $filename = uniqid('item_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $filename);
                $image = $filename;
            }
        }
        $stmt = $pdo->prepare("INSERT INTO items (name, description, category_id, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $cat_id, $price, $image]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Item added!'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => '⚠️ Please fill all required fields.'];
    }
    header('Location: items.php'); exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM items WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['image'] && file_exists('../uploads/' . $row['image'])) {
        unlink('../uploads/' . $row['image']);
    }
    $pdo->prepare("DELETE FROM items WHERE id = ?")->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Item deleted.'];
    header('Location: items.php'); exit;
}

// EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id     = (int)$_POST['id'];
    $name   = trim($_POST['name'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $cat_id = (int)($_POST['category_id'] ?? 0);
    $price  = (float)($_POST['price'] ?? 0);
    $image  = null;

    if (!empty($_FILES['image']['name'])) {
        $ext     = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array(strtolower($ext), $allowed)) {
            $filename = uniqid('item_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $filename);
            $image = $filename;
        }
    }

    if ($name && $cat_id && $price > 0) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE items SET name=?, description=?, category_id=?, price=?, image=? WHERE id=?");
            $stmt->execute([$name, $desc, $cat_id, $price, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE items SET name=?, description=?, category_id=?, price=? WHERE id=?");
            $stmt->execute([$name, $desc, $cat_id, $price, $id]);
        }
        $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Item updated.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => '⚠️ Please fill all required fields.'];
    }
    header('Location: items.php'); exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT i.*, c.name as cat_name
    FROM items i JOIN categories c ON i.category_id = c.id
    ORDER BY i.id DESC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Items';
include 'header.php';
?>

<?php include 'footer.php'; ?>