<?php // Admin items - PDO with image upload
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

<style>
.page-grid { display:grid; grid-template-columns:380px 1fr; gap:1.5rem; align-items:start; }
.item-thumb {
    width:44px; height:44px; border-radius:8px;
    object-fit:cover; background:#FFF0E6;
    display:flex; align-items:center; justify-content:center;
    font-size:1.4rem; overflow:hidden; border:1px solid var(--border);
}
.item-thumb img { width:100%; height:100%; object-fit:cover; }
</style>

<div class="page-grid">
    <!-- ADD FORM -->
    <div class="card">
        <div class="card-header">Add New Item</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Chicken Pizza" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" placeholder="Short description…"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select</option>
                            <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs) *</label>
                        <input type="number" name="price" class="form-control" placeholder="0" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="add" class="btn btn-primary">+ Add Item</button>
            </form>
        </div>
    </div>

    <!-- ITEMS LIST -->
    <div class="card">
        <div class="card-header">
            All Items <span style="font-size:0.8rem;color:var(--gray);font-weight:400">(<?= count($items) ?>)</span>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Image</th><th>Name</th>
                        <th>Category</th><th>Price</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($items)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:2rem">No items yet</td></tr>
                <?php else: ?>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td>
                            <div class="item-thumb">
                                <?php if($item['image'] && file_exists('../uploads/'.$item['image'])): ?>
                                    <img src="/foodbyte/uploads/<?= htmlspecialchars($item['image']) ?>" alt="">
                                <?php else: ?>
                                    <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <div style="font-size:0.77rem;color:var(--gray);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                <?= htmlspecialchars($item['description']) ?>
                            </div>
                        </td>
                        <td><span class="badge badge-preparing"><?= htmlspecialchars($item['cat_name']) ?></span></td>
                        <td style="font-weight:700;color:var(--orange)">Rs <?= number_format($item['price'],0) ?></td>
                        <td>
                            <div style="display:flex;gap:0.4rem">
                                <button class="btn btn-outline btn-sm"
                                    onclick="openEditItem(<?= $item['id'] ?>, '<?= addslashes($item['name']) ?>', '<?= addslashes($item['description']) ?>', <?= $item['category_id'] ?>, <?= $item['price'] ?>)">
                                    ✏️ Edit
                                </button>
                                <a href="?delete=<?= $item['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this item?')">🗑</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="editItemModal">
    <div class="modal" style="text-align:left;max-width:480px">
        <h3 style="margin-bottom:1.2rem">Edit Item</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editItemId">
            <div class="form-group">
                <label>Item Name *</label>
                <input type="text" name="name" id="editItemName" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="editItemDesc" class="form-control"></textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" id="editItemCat" class="form-control" required>
                        <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (Rs) *</label>
                    <input type="number" name="price" id="editItemPrice" class="form-control" min="0" step="0.01" required>
                </div>
            </div>
            <div class="form-group">
                <label>New Image (optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="modal-actions" style="justify-content:flex-start;margin-top:1rem">
                <button type="submit" name="edit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-outline"
                    onclick="document.getElementById('editItemModal').classList.remove('show')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditItem(id, name, desc, catId, price) {
    document.getElementById('editItemId').value    = id;
    document.getElementById('editItemName').value  = name;
    document.getElementById('editItemDesc').value  = desc;
    document.getElementById('editItemCat').value   = catId;
    document.getElementById('editItemPrice').value = price;
    document.getElementById('editItemModal').classList.add('show');
}
</script>

<?php include 'footer.php'; ?>